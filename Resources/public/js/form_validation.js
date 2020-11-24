$(function () {
    $("body")
        .on('keydown', '.integer_validation', function(event){
            return ( event.ctrlKey || event.altKey
                || (48<=event.keyCode && event.keyCode<=57 && event.shiftKey===false)//default num keys
                || (96<=event.keyCode && event.keyCode<=105)                        //num pad num keys
                || (event.keyCode===8) || (event.keyCode===9)                         //backspace and tab
                || (35<=event.keyCode && event.keyCode<=46)                         //end, home, navigate keys, insert, delete
                || (event.keyCode===13)
            );
        })
        .on('keydown', '.float_validation', function(event){
            return ( event.ctrlKey || event.altKey
                || (48<=event.keyCode && event.keyCode<=57 && event.shiftKey===false)//default num keys
                || (96<=event.keyCode && event.keyCode<=105)                        //num pad num keys
                || (event.keyCode===8) || (event.keyCode===9)                         //backspace and tab
                || (35<=event.keyCode && event.keyCode<=46)                         //end, home, navigate keys, insert, delete
                || (event.keyCode===110) || (event.keyCode===190)                     //dot(num pad) and dot
                || (event.keyCode=== 13)                                             //enter
            );
        })
        .on('keydown', '.integer_neg_validation', function(event){
            return ( event.ctrlKey || event.altKey
                || (48<=event.keyCode && event.keyCode<=57 && event.shiftKey===false)//default num keys
                || (96<=event.keyCode && event.keyCode<=105)                        //num pad num keys
                || (event.keyCode===8) || (event.keyCode===9)                         //backspace and tab
                || (35<=event.keyCode && event.keyCode<=46)                         //end, home, navigate keys, insert, delete
                || (event.keyCode===109) || (event.keyCode===173 && event.shiftKey===false)//-(num pad) and -
                || (event.keyCode=== 13)                                             //enter
            );
        })
        .on('keydown', '.float_neg_validation', function(event){
            return ( event.ctrlKey || event.altKey
                || (48<=event.keyCode && event.keyCode<=57 && event.shiftKey===false)//default num keys
                || (96<=event.keyCode && event.keyCode<=105)                        //num pad num keys
                || (event.keyCode===8) || (event.keyCode===9)                         //backspace and tab
                || (35<=event.keyCode && event.keyCode<=46)                         //end, home, navigate keys, insert, delete
                || (event.keyCode===110) || (event.keyCode===190)                     //dot(num pad) and dot
                || (event.keyCode===109) || (event.keyCode===173 && event.shiftKey===false)//-(num pad) and -
                || (event.keyCode=== 13)                                             //enter
            );
        })
        .on('keydown', '.tel_validation', function(event){
            return ( event.ctrlKey || event.altKey
                || (48<=event.keyCode && event.keyCode<=57 && event.shiftKey===false)//default num keys
                || (96<=event.keyCode && event.keyCode<=105)                        //num pad num keys
                || (event.keyCode===8) || (event.keyCode===9)                         //backspace and tab
                || (35<=event.keyCode && event.keyCode<=46)                         //end, home, navigate keys, insert, delete
                || (event.keyCode===61 && event.shiftKey===true) || (event.keyCode===107)//+ , +(num pad)
                || (event.keyCode=== 13)                                             //enter
            );
        });
});