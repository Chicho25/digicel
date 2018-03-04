
<html>
    <head>
        <title>How To Send Email Using Mailjet API</title>
        <meta name="robots" content="noindex, nofollow">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <script src="js/bootstrap.js" type="text/javascript"></script>

        <meta name="robots" content="noindex, nofollow">
         <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-43981329-1']);
            _gaq.push(['_trackPageview']);
            (function() {
                var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();
        </script>

    </head>
    <body>
        <div class="container">
            <div class='wrap'>
                <div class='row'>
                    <div id="main" class="col-xs-12 col-sm-6 col-md-4 ">
                        <h1 id='h1'>Send Email via Mailjet API Using PHP</h1>
                    </div>
                </div>

                <div class='row' >
                    <div id="login" class="col-xs-12 col-sm-6 col-md-4 ">
                        <div style='margin-top:-30px;'>
                            <h2>Email Form</h2>
                            <div style="margin: 20px 10px;">
                                <form action="" method="post" name='myForm' onsubmit="return validate();">
                                    <label>To : </label>
                                    <input type='text' name='to' placeholder="Enter Reciever's Email Address"/>
                                    <label>Subject : </label>
                                    <input type='text' name='subject' placeholder='Subject'/>
                                    </br><label>     </label>
                                    <label>Message : </label><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;"; ?><input type="radio" name='msg' value='txt'><?php echo "&nbsp;"; ?>Text <?php echo "&nbsp;&nbsp;"; ?><input type="radio" name='msg' value='html'><?php echo "&nbsp;"; ?> Html
                                    </br><label></label>
                                    <textarea name='text' style="height: 134px; resize: none; width: 100%;" placeholder='Write your message here...'/></textarea><br/>
                                    <br/>
                                    <input type='submit' name='submit' value='send' /><br/>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <div class='row'>
                    <div id="note" class="col-xs-12 col-sm-6 col-md-4 ">
                        <b></b>
                    </div>
                </div>
            </div>
        </div>



        <script type="text/javascript">
            function validate()
            {

                if (document.myForm.to.value == "")
                {
                    alert("Please enter your Email!");
                    document.myForm.to.focus();
                    return false;
                }
                else
                {

                    /*validating email with strong regular expression(regex)*/
                    var str1 = document.myForm.to.value
                    /* This is the regular expression string to validate the email address
                     Email address example : john@yahoo.com ,  john@yahoo.net.com , john.mary@yahoo.org ,
                     john.mary@yahoo.rediff-.org ,  john.mary@yahoo.rediff-.org.com
                     */

                    var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([com net org]{3}(?:\.[a-z]{6})?)$/i
                    if (!filter.test(str1))
                    {

                        alert("Please enter a valid email address!")
                        document.myForm.to.focus();
                        return false;
                    }
                    if (document.myForm.subject.value == "")
                    {
                        alert("Please enter a subject!");
                        document.myForm.subject.focus();
                        return false;
                    }
                    if (document.myForm.text.value == "")
                    {
                        alert("Please enter a text!");
                        document.myForm.text.focus();
                        return false;
                    }



                    return(true);
                }
            }
        </script>
    </body>
</html>
<?php
include("src/Mailjet/php-mailjet-v3-simple.class.php");

$apiKey = 'a65e4a94cfb69e119f2650cc394fd958';
$secretKey = '100dfed2fc46c031c6bc242e108e7f93';

$mj = new Mailjet($apiKey, $secretKey);
if (isset($_POST['submit'])) {

    function sendEmail() {
        // Create a new Object
        $mj = new Mailjet();
        $params = array(
            "method" => "POST",
            "from" => "parrieta@dchain.com",
            "to" => "{$_POST['to']}",
            "subject" => "{$_POST['subject']}",
            "text" => "{$_POST['text']}",
            'Attachments' => array(attachment('test.txt', './test.txt'))
        );
        $result = $mj->sendEmail($params);
        if ($mj->_response_code == 200) {
            //echo "success - email sent";
            print '<script type="text/javascript">';
            print 'alert("email successfully sent!")';
            print '</script>';
        } elseif ($mj->_response_code == 400) {
            //echo "error - " . $mj->_response_code;
            print '<script type="text/javascript">';
            print 'alert("Bad Request! One or more arguments are missing or maybe mispelling.")';
            print '</script>';
        } elseif ($mj->_response_code == 401) {
            //echo "error - " . $mj->_response_code;
            print '<script type="text/javascript">';
            print 'alert("Unauthorized! You have specified an incorrect ApiKey or username/password couple.")';
            print '</script>';
        } elseif ($mj->_response_code == 404) {
            //echo "error - " . $mj->_response_code;
            print '<script type="text/javascript">';
            print 'alert("Not Found! The method your are trying to reach don\'t exists.")';
            print '</script>';
        } elseif ($mj->_response_code == 405) {
            //echo "error - " . $mj->_response_code;
            print '<script type="text/javascript">';
            print 'alert("Method Not Allowed! You made a POST request instead of GET, or the reverse.")';
            print '</script>';
        } else {
            print '<script type="text/javascript">';
            print 'alert(" Internal Server Error! Status returned when an unknow error occurs")';
            print '</script>';
        }

        return $result;
    }

    sendEmail();
}
?>
