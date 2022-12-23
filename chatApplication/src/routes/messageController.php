<?php 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/messages/get-messages/{userID}', function(Request $request,Response $response, $args ){
    try{
        $pdo = new DB();
        $pdo = $pdo->connect();
        
        $reciverID = $request->getAttribute('userID');
        
        //query to get the messages based on the user's id.
        $messageQuery = "SELECT message_id, message_content, message_sender, message_sender_name,message_time FROM messages WHERE message_receiver = :receiver_id";
        
        $stmt = $pdo->prepare($messageQuery);
        $stmt->bindParam(':receiver_id', $reciverID);
        $stmt->execute();

        $messages = [];

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $messages[]=[
                'message_id' => $row['message_id'],
                'message_content' => $row['message_content'],
                'message_sender' => $row['message_sender'],
                'message_sender_name' => $row['message_sender_name'],
                'message_time' => $row['message_time']
            ];
        }

        $messagesPayload = json_encode($messages);

        $response->getBody()->write($messagesPayload);
        
        return $response->withHeader('Content-type','application/json');
    } catch(PDOException $e){
        $response->getBody()->write($e->getMessage());
    }
    
    
});

$app->post('/messages/send-message', function(Request $request, Response $response, $args){
    /*
        For the send message API endpoint we are going to receive the messade details such as message content, message sender id, message receiver id, message time from the request body. We use the following code to convert the JSON values from the request body into PHP native array format.
    */
    $contentType = $request->getHeaderLine("Content-type");
    if(strstr($contentType, "application/json")){
        $contents = json_decode(file_get_contents('php://input'),true);
        if(json_last_error() == JSON_ERROR_NONE){
            $request = $request->withParsedBody($contents);
        }
    }
    $parsedBody = $request->getParsedBody();

    /*
        We use the Validation class defined in validation.php to validate the new message contents. We can use the Validation class in the future to add further validation function for different API endpoints, or we can modify it and use it to check for constraints such as Dateformat in the message time string, or the content length.
    */
    $validator = new Validation();
    $error = $validator->validateNewMessage($parsedBody);
    if(!$error){
        try{
            $pdo = new DB();
            $pdo = $pdo->connect();

            $messageContent = $parsedBody['message_content'];
            $messageSender = $parsedBody['message_sender'];
            $messageReceiver = $parsedBody['message_receiver'];
            $messageTime = $parsedBody['message_time'];

            /*
                To add the sender name to the insert statement, we have to acquire it from the user table. We use the sender id from the parsed request body to get the username. If no username is present then the sender id is used as the username. We can modify this to throw an exception if the user is not present in the database.
            */

            $usernameQuery = "SELECT username FROM user WHERE user_id=:user_id";
            $stmt1 = $pdo->prepare($usernameQuery);
            $stmt1->bindParam(':user_id',$message_sender);
            $stmt1->execute();
            $messageSenderName = $stmt1->fetchColumn();
            if(!$messageSenderName){
                $messageSenderName = $messageSender;
            }

            $insertQuery = "INSERT INTO messages(message_content,message_receiver,message_sender,message_sender_name,message_time) VALUES(:message_content,:message_receiver,:message_sender,:message_sender_name, :message_time)";

            $stmt2 = $pdo->prepare($insertQuery);
            $stmt2->bindParam(':message_content',$messageContent);
            $stmt2->bindParam(':message_receiver',$messageReceiver);
            $stmt2->bindParam(':message_sender', $messageSender);
            $stmt2->bindParam(':message_sender_name',$messageSenderName);
            $stmt2->bindParam(':message_time',$messageTime);

            $stmt2->execute();
            $response->getBody()->write("Message successfully sent");

        }catch(PDOException $e){
            $response->getBody()->write($e.getMessage());
        }
    } else{
        $response->getBody()->write($error);
    }
        
    return $response;
});

$app->get('/messages/message-by-sender', function(Request $request, Response $response, $args){
    /*
        To get the messages sent by a particular user to the current user.
     */
    $contentType = $request->getHeaderLine("Content-type");
    if(strstr($contentType, "application/json")){
        $contents = json_decode(file_get_contents('php://input'),true);
        if(json_last_error() == JSON_ERROR_NONE){
            $request = $request->withParsedBody($contents);
        }
    }
    $parsedBody = $request->getParsedBody();

    $receiverID = $parsedBody['receiver_id'];
    $senderID = $parsedBody['sender_id'];

    try{
        $pdo = new DB();
        $pdo = $pdo->connect();

        $query = "SELECT message_id, message_content, message_sender_name,message_time FROM messages WHERE message_receiver = :receiver_id AND message_sender=:sender_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':receiver_id',$receiverID);
        $stmt->bindParam(':sender_id',$senderID);

        $stmt->execute();
        $messages = [];

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $messages[]=[
                'message_id' => $row['message_id'],
                'message_content' => $row['message_content'],
                'message_sender_name' => $row['message_sender_name'],
                'message_time' => $row['message_time']
            ];
        }

        $messagesPayload = json_encode($messages);

        $response->getBody()->write($messagesPayload);
        
        return $response->withHeader('Content-type','application/json');
    
    }catch(PDOException $e){
        $response->getBody()->write($e.getMessage());
        return $response;
    }    
});

$app->post('/messages/delete-message/{messageID}', function(Request $request,Response $response,$args){
        $messageID = $request->getAttribute('messageID');
        try{
            $pdo = new DB();
            $pdo = $pdo->connect();
            
            $query = "DELETE FROM messages WHERE message_id = :message_id";
            $stmt = $pdo->prepare($query);

            $stmt->bindParam(':message_id',$messageID);
            $stmt->execute();
            $response->getBody()->write("Message Deleted");
        }catch(PDOException $e){
            $response->getBody()->write($e.getMessage());
        }

        return $response;
});
?>