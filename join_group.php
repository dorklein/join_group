<?php
header('Content-Type: text/html; charset=utf-8');  //utf-8

date_default_timezone_set('Asia/Jerusalem');   //set israel time

// לשים את הטוקן של הבוט
define('API_TOKEN', '~~~~');

// לשים קישור של הקבוצה 
define('GRUP_ֹLINK', '~~~~');

$update = file_get_contents('php://input');   
$update = json_decode($update, TRUE);   

function curlPost($method,$datas=[]){

    $urll = "https://api.telegram.org/bot".API_TOKEN."/".$method;
	
    $ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$urll);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
   
    $res = curl_exec($ch);
	file_put_contents("log_join.txt", $res);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
		curl_close($ch);
    }else{
		curl_close($ch);
        return json_decode($res,TRUE);
      
    }
}

function CheckId($chatId){

		$tempid = file_get_contents('joinChatID.txt');
		$pos1 = stripos($tempid, (string)$chatId);

			if ($pos1 === false) {
				WriteChatID($chatId);
			}
	}
	function WriteToFile($write){
		$myfile = fopen('log.txt', 'a');
			fwrite($myfile,$write);
			fwrite($myfile,"\n\n");
			fclose($myfile);
	}

	function WriteChatID($write){
		$myfile = fopen('joinChatID.txt', 'a');
			fwrite($myfile,$write);
			fwrite($myfile,"\r\n");
			fclose($myfile);
	}

	function SumAllusers(){
	
		$myfile = fopen('joinChatID.txt', 'r');
		$sum = 0;
		
		while (!feof($myfile)) {
			$contents = fgets($myfile, 20);
			$sum += 1;
		}
		return $sum;
}

function sendMessage($id, $message, $reply_markup = NULL){

$PostData = array(
    'chat_id' => $id,
    'text' => $message,
    'parse_mode' => "markdown", 
    'reply_markup' => $reply_markup,
    'disable_web_page_preview' => true
);
$out = curlPost('sendMessage',$PostData);
return $out;
}

function forwardMessage($id, $chatId, $messageId){

$PostData = array(
    'chat_id' => $id,
    'from_chat_id' => $chatId,
    'message_id' => $messageId
);
curlPost('forwardMessage',$PostData);

}

function 
editMessageText($chatId, $messageId, $text, $reply_markup = null){

$PostData = array(
    'chat_id' => $chatId,
    'message_id' => $messageId,
    'text' => $text,
    'parse_mode' => "Markdown",
    'disable_web_page_preview' => true,
    'reply_markup' => $reply_markup
);
$ed = curlPost('editMessageText',$PostData);
return $ed;
}

function sendChatAction($chatId, $action){
$PostData = array(
    "chat_id" => $chatId, 
    "action" => $action
);
curlPost("sendChatAction", $PostData);
}

//כל סוגי העדכונים שמקבלים מטלגרם
$messageId = $update["message"]["message_id"];
$message = $update["message"]["text"];
$fromId = $update["message"]["from"]["id"];
$chatId = $update["message"]["chat"]["id"];
$callData = $update["callback_query"]["data"];
$callFromId = $update["callback_query"]["from"]["id"];
$callId = $update["callback_query"]["id"];
$callMessageId = $update["callback_query"]["message"]["message_id"];
$photo = $update["message"]["photo"];
$photoId = $update["message"]["photo"]["0"]["file_id"];
$FromId = $update["message"]["from"]["id"];
$docId = $update["message"]["document"]["file_id"];
$docName = $update["message"]["document"]["file_name"];
$docMime = $update["message"]["document"]["mime_type"];
$docSize = $update["message"]["document"]["file_size"];
$fileCaption =  $update["message"]["caption"];
$inlineQ = $update["inline_query"]["query"];
$InlineQId = $update["inline_query"]["id"];
$InlineMsId = $update["callback_query"]["inline_message_id"];
$contect = $update["message"]["contact"];
$contectNom = $update["message"]["contact"]["phone_number"];
$contectFirstName = $update["message"]["contact"]["first_name"];
$contectLastName = $update["message"]["contact"]["last_name"];
$contectId = $update["message"]["contact"]["user_id"];


//מוסיף את האיידיים לקובץ
if(!is_null($chatId)){
	CheckId($chatId);
	}

//הכפתור לבקשת המספר טלפון
$reply_markup = json_encode(array('keyboard' => array(array(array(
    "text" => "מאשר את הכללים",
    "request_contact" => true))),
    'one_time_keyboard' => true,
	'resize_keyboard' => true));

//הכפתור עם קישוק לקבוצה למשתמשים חדשים			 
$reply_markup2 = json_encode(array(
'inline_keyboard' => array(array(array(
             'text' => 'הקבוצה....', //אפשר לשנות  את האקסט של הכפתור
             'url' => GRUP_ֹLINK 
			 )), array(array(
             'text' => 'הערוץ....',
             'url' => 'https://t.me/~~~' //להוסיף קישור לערוץ
             )), array(array(
             'text' => '💬 פנייה לצוות  💬',
             'url' => 'https://t.me/~~~~' //להוסיף קישור לאיש צוות
             )), array(array(
			 'text' => 'אודות',
			 'callback_data' => "אודות"
			 )))));
			 
			 
//הטקסט כאשר נכנסים לרובוט /start
$welcomText = 'כללי הקבוצה.....'; //להוסיף את כללי הקבוצה


//הטקסט כאשר המשתמש הוא חדש
$newMemberText = " 👇🏻 לחצו על קישור ותהנו משירות יעיל ומהיר.
🚫 זכרו, לשמור על הכללים!

צוות אלי/ה סרטים 😌";


if ($message == "/start"){
//כותב... למעלה
sendChatAction($chatId, "typing");	

sendMessage($chatId, $welcomText, $reply_markup);
} 

if (isset($contect)){

forwardMessage(~~~~, $chatId, $messageId); 
//לשים איידי של ערוץ שלשם יועבר כל האנשי קשר.
//שימו לב, הרובוט לא יוכל לעביר את האנשי קשר אם הוא לא מנהל בערוץ.

sendChatAction($chatId, "typing");	
$sen = sendMessage($chatId, "נא המתן/י מספר שניות, המערכת מעבדת את המידע....");

$senId = $sen["result"]["message_id"];
$senChatId = $sen["result"]["chat"]["id"];

//כותב כותב... למעלה
sendChatAction($chatId, "typing");	

//חדש, לא נמצא בשום קבוצה
$edit = editMessageText($senChatId, $senId, $newMemberText, $reply_markup2);


}

if($callData == "אודות"){
	
	//אפשר לשנות את הטקסט ואת גרסת הבוט במקרה הצורך, אבל בבקשה אל תסירו את השורה האחרונה של הקרדיט.
$PostData = array(
    "callback_query_id" => $callId, 
    "text" => "רובוט צירוף לקבוצה...
	〰 גרסה 1.0.0〰
	רובוט זה נכתב נוצר ע\"י @Dor_klein",
	"show_alert" => true
);
curlPost("answerCallbackQuery", $PostData);
	
}


?>