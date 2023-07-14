<?php
    function get_doesHaveLinks($mailText) {
        if (strpos($mailText, 'http') != false)
            return true;
        if (strpos($mailText, 'www') != false)
            return true;
        return false;
    }

    function get_doesHaveSpammyWords($mailText) {
        $spammyWords = array('free', 'award', 'claim', 'text', 'call', 'urgent', 'txt', 'win', 'won', 'congrat');
        for ($i = 0; $i < sizeof($spammyWords); $i++) {
            $spammyWord = $spammyWords[$i];
            if (stripos($mailText, $spammyWord) != false)
                return true;
        }
        return false;
    }

    function get_lengthOfText($mailText) {
        return strlen($mailText);
    }

    function make_prediction($doesHaveLinks, $doesHaveSpammyWords, $lengthOfText) {
        // Based on the features,
        // decide if the email is spam with the weka decision tree
        // The decision procedure will be output for viewing
        $isSpam = false;
        echo 'Decision procedure:<br>';
        if ($doesHaveSpammyWords) {
            echo 'doesHaveSpammyWords: true<br>';
            if ($lengthOfText <= 97) {
                echo 'lengthOfText <= 97: true, length is ' . $lengthOfText . '<br>';
                if ($doesHaveLinks) {
                    echo 'doesHaveLinks: true<br>';
                    $isSpam = true;           
                } 
                else {
                    echo 'doesHaveLinks: false<br>';
                }
            }
            else {
                echo 'lengthOfText <= 97: false, length is ' . $lengthOfText . '<br>'; 
                if ($lengthOfText <= 176) {
                    echo 'lengthOfText <= 176: true, length is ' . $lengthOfText . '<br>';
                    $isSpam = true;
                }               
                else {
                    echo 'lengthOfText <= 176: false, length is ' . $lengthOfText . '<br>';                    
                }
            }
        } 
        else {
            echo 'doesHaveSpammyWords: false<br>';
            if ($doesHaveLinks) {
                echo 'doesHaveLinks: true<br>';
                return $isSpam = true;
            }
            else {
                echo 'doesHaveLinks: false<br>';
            }
        }

        return $isSpam;
    }

    function output($isSpam) {
        echo '<br>Conclusion:<br>';
        if ($isSpam)
            echo "spam";
        else
            echo "ham";
    }

    function main() {
        $text_message = $_POST['text_message'];
        $doesHaveLinks = get_doesHaveLinks($text_message);
        $doesHaveSpammyWords = get_doesHaveSpammyWords($text_message);
        $lengthOfText = get_lengthOfText($text_message);
        
        $isSpam = make_prediction($doesHaveLinks, $doesHaveSpammyWords, $lengthOfText);

        output($isSpam);
    }

    main();
?>