<?php
class Validation{
    /*
        Instead of using a third party middleware for validation, we can use the below validateNewMessage method to validate the contents of the new message. Right now I have just added validation for datatypes of the content. The below code can be modified to verify string length or even the presence of the message_sender and message_receiver ids in the database. or to validate the DateTime format against ISO standards or any standards we might use.
    */
    public function validateNewMessage($newMessage){
        
        $error = '';
        
        if(!is_string($newMessage['message_content'])) $error = $error."Message content should be a string. \n";
        
        if(!is_int($newMessage['message_sender'])) $error = $error."Sender id should be an Integer. \n";
        
        if(!is_int($newMessage['message_receiver'])) $error = $error."Receiver id should be an Integer. \n";
        
        if(!is_string($newMessage['message_time'])) $error = $error."Message time should be converted to ISO string before sending. \n";

        return $error;

    }
}
?>