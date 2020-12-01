<?php

namespace PouyaSoft\AppzaBundle\Services;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use App\Entity\SMS as SMSEntity;
use App\Entity\User;
use GuzzleHttp\Client;

class SMS
{
    private $apiKey = '';
    private $SecretKey = '';
    private $LineNumber = '';

    private $env;
    private $restClient;

    public function __construct(Client $restClient, $env)
    {
        $this->restClient = $restClient;
        $this->env = $env;
    }

    private function getToken()
    {
        try {
            $response = $this->restClient->post('http://RestfulSms.com/api/Token', ['json' => [
                    'UserApiKey' => $this->apiKey,
                    'SecretKey' => $this->SecretKey
            ]]);
            $result = json_decode($response->getBody(), true);

            return [
                'status' => $result['IsSuccessful'] ? 'success': 'danger',
                'message' => $result['Message'],
                'tokenKey' => $result['TokenKey'],
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'danger',
                'message' => $ex->getMessage(),
                'tokenKey' => null,
            ];
        }
    }

    private function getStatus($messageId)
    {
        $tokenResult = $this->getToken();
        if($tokenResult['status'] != 'success')
            return $tokenResult;
        dump($tokenResult);

        try {
            $response = $this->restClient->get('http://RestfulSms.com/api/MessageSend'. '?' . http_build_query([
                    'id' => $messageId,
                ]), [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-sms-ir-secure-token: '.$tokenResult['tokenKey']]
            ]);
            $result = json_decode($response->getBody(), true);

            return [
                'status' => $result['IsSuccessful'] ? 'success': 'danger',
                'message' => $result['Message'],
//                'verificationCodeId' => $result['VerificationCodeId'],
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'danger',
                'message' => $ex->getMessage(),
//                'verificationCodeId' => null,
            ];
        }
    }

    private function sendVerification($code, $mobile)
    {
        $tokenResult = $this->getToken();
        if($tokenResult['status'] != 'success')
            return $tokenResult;
//        dump($tokenResult);

        try {
            $response = $this->restClient->post('http://RestfulSms.com/api/VerificationCode', ['json' => [
                'Code' => $code,
                'MobileNumber' => $mobile,
            ]], [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-sms-ir-secure-token: '.$tokenResult['tokenKey']]
            ]);
            $result = json_decode($response->getBody(), true);

            return [
                'status' => $result['IsSuccessful'] ? 'success': 'danger',
                'message' => $result['Message'],
                'verificationCodeId' => $result['VerificationCodeId'],
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'danger',
                'message' => $ex->getMessage(),
                'verificationCodeId' => null,
            ];
        }
    }

    private function sendFastWithTemplate(array $params, $mobile, $templateId)
    {
        $tokenResult = $this->getToken();
        if($tokenResult['status'] != 'success')
            return $tokenResult;
//        dump($tokenResult);

        $parameterArray = [];
        foreach ($params as $paramKey => $paramValue) {
            $parameterArray []= ["Parameter" => $paramKey, "ParameterValue" => $paramValue];
        }

        try {
            $response = $this->restClient->post('http://RestfulSms.com/api/UltraFastSend', ['json' => [
                'ParameterArray' => $parameterArray,
                'Mobile' => $mobile,
                'TemplateId' => $templateId,
            ]], [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-sms-ir-secure-token: '.$tokenResult['tokenKey']]
            ]);
            $result = json_decode($response->getContent(), true);

            return [
                'status' => $result['IsSuccessful'] ? 'success': 'danger',
                'message' => $result['Message'],
                'verificationCodeId' => $result['VerificationCodeId'],
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'danger',
                'message' => $ex->getMessage(),
                'verificationCodeId' => null,
            ];
        }
    }

    /**
     * @param array $mobileMessageKeyValue   example: ['09121231234' => 'test message']
     * @param DateTime $sendDateTime        null => send now | else => send at date
     * @param bool $continueInError
     * @return array
     */
    private function messageSend($mobileMessageKeyValue, $sendDateTime = null, $continueInError = true)
    {
        $tokenResult = $this->getToken();
        if($tokenResult['status'] != 'success')
            return $tokenResult;

        try {
            $response = $this->restClient->post('http://RestfulSms.com/api/MessageSend', json_encode([
                'MobileNumbers' => array_keys($mobileMessageKeyValue),
                'Messages' => array_values($mobileMessageKeyValue),
                'LineNumber' => $this->LineNumber,
                'SendDateTime' => $sendDateTime ? $sendDateTime->format('Y-m-d\TH:i:s') : null,
                'CanContinueInCaseOfError' => $continueInError
            ]), [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-sms-ir-secure-token: '.$tokenResult['tokenKey']]
            ]);
            $result = json_decode($response->getContent(), true);

            return [
                'status' => $result['IsSuccessful'] ? 'success': 'danger',
                'message' => $result['Message'],
                'ids' => $result['Ids'],
                'batchKey' => $result['BatchKey'],
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'danger',
                'message' => $ex->getMessage(),
                'verificationCodeId' => null,
            ];
        }
    }

    private function sendFast(User $user, $mobile, $templateId, $type, $params, EntityManager $em)
    {
        $smsEntity = new SMSEntity();
        $smsEntity->setType($type);
        $smsEntity->setUser($mobile ? null : $user);
        $smsEntity->setDate(new DateTime());
        $smsEntity->setMessage($templateId);
        $smsEntity->setMobile($mobile ? $mobile : $user->getMobile());
        $smsEntity->setDelivery(SMSEntity::DELIVERY_NOT_SENT);

        if($this->env == 'env') {
            $smsEntity->setDelivery(SMSEntity::DELIVERY_INACTIVE);
            $em->persist($smsEntity);
            $em->flush();
            return [
                'status' => 'danger',
                'message' => 'ارسال پیامک غیر فعال است.',
                'verificationCodeId' => null,
            ];
        }

        $result = $this->sendFastWithTemplate($params, $smsEntity->getMobile(), $templateId);

        $smsEntity->setDelivery($result['status'] == 'success' ? SMSEntity::DELIVERY_RECEIVED : SMSEntity::DELIVERY_NOT_SENT);

        $em->persist($smsEntity);
        $em->flush();

        return $result;
    }

    public function sendMessage(User $user, $mobile, $message, $type, EntityManager $em)
    {
        $smsEntity = new SMSEntity();
        $smsEntity->setType($type);
        $smsEntity->setUser($mobile ? null : $user);
        $smsEntity->setDate(new DateTime());
        $smsEntity->setMessage($message);
        $smsEntity->setMobile($mobile ? $mobile : $user->getMobile());
        $smsEntity->setDelivery(SMSEntity::DELIVERY_NOT_SENT);

        if($this->env == 'env') {
            $smsEntity->setDelivery(SMSEntity::DELIVERY_INACTIVE);
            $em->persist($smsEntity);
            $em->flush();
            return [
                'status' => 'danger',
                'message' => 'ارسال پیامک غیر فعال است.',
                'verificationCodeId' => null,
            ];
        }

        $result = $this->messageSend([$smsEntity->getMobile() => $message]);

        $smsEntity->setDelivery($result['status'] == 'success' ? SMSEntity::DELIVERY_RECEIVED : SMSEntity::DELIVERY_NOT_SENT);

        $em->persist($smsEntity);
        $em->flush();

        return $result;
    }

//    public function userVerifyCode(User $user, EntityManager $em)
//    {
//        return $this->sendFast($user, null, 4904, SMSEntity::TYPE_LOGIN, [
//            'password' => $user->getVerifyCode(),
//        ], $em);
//    }
//
//    public function userSendPassword(User $user, UserManager $userManager, EntityManager $em)
//    {
//        $plainPassword = $user->getCodeMelli();
//        $user->setPlainPassword($plainPassword);
//        $user->setPasswordSent(true);
//        $userManager->updateUser($user);
//
//        return $this->sendFast($user, null, 4943, SMSEntity::TYPE_ACTIVATION, [
//            'username' => $user->getUsername(),
//            'password' => $plainPassword,
//        ], $em);
//    }
//
//    public function userResettingPassword(User $user, $url, EntityManager $em)
//    {
//        return $this->sendFast($user, null, 5047, SMSEntity::TYPE_FORGET_PASSWORD, [
//            'url' => $url,
//        ], $em);
//    }

    public function userLogin(User $user, $password, EntityManager $em)
    {
        return $this->sendFast($user, null, 19759, SMSEntity::TYPE_LOGIN, [
            'password' => $password,
        ], $em);
    }
}