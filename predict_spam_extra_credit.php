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

    function get_excitation($mailText) {
        $excitation = substr_count($mailText, '!');
        return $excitation;
    }

    function get_doesHavePhoneNumber($mailText) {
        $pattern = '/\d{11}|\d{4}[\s]\d{3}[\s]\d{4}|\d{4}[\-]\d{3}[\-]\d{4}/';
        if (preg_match($pattern, $mailText) != false)
            return true;
        return false;
    }

    function get_doesHaveUnusualSymbol($mailText) {
        $unusualWords = array('<', '>', '+', '$', '/', '-', '&');
        for ($i = 0; $i < sizeof($unusualWords); $i++) {
            $unusualWord = $unusualWords[$i];
            if (stripos($mailText, $unusualWord) != false)
                return true;
        }
        return false;
    }

    function make_prediction($doesHaveLinks, $doesHaveSpammyWords, $lengthOfText, 
        $excitation, $doesHavePhoneNumber, $doesHaveUnusualSymbol) {
        // Based on the features,
        // decide if the email is spam with the weka decision tree
        // The decision procedure will be output for viewing
        $isSpam = false;
        echo 'Decision procedure:<br>';

        if ($doesHavePhoneNumber == false) {
            echo 'doesHavePhoneNumber: false<br>';
            if ($doesHaveUnusualSymbol == false) {
                echo 'doesHaveUnusualSymbol: false<br>';
                if ($doesHaveSpammyWords == false) {
                    echo 'doesHaveSpammyWords: false<br>';
                    if ($doesHaveLinks == false) {
                        echo 'doesHaveLinks: false<br>';
                    }
                    else {
                        echo 'doesHaveLinks: true<br>';
                        $isSpam = true;
                    }
                }
                else {
                    echo 'doesHaveSpammyWords: true<br>';
                    if ($lengthOfText <= 103) {
                        echo 'length <= 103: true, length is ' . $lengthOfText . '<br>';
                    }
                    else {
                        echo 'length <= 103: false, length is ' . $lengthOfText . '<br>';
                        if ($lengthOfText <= 163) {
                            echo 'length <= 163: true, length is ' . $lengthOfText . '<br>';
                            if ($excitation <= 0) {
                                echo 'excitation <= 0: true<br>';
                                if ($doesHaveLinks == false) {
                                    echo 'doesHaveLinks: false<br>';
                                }
                                else {
                                    echo 'doesHaveLinks: true<br>';
                                    if ($lengthOfText <= 152) {
                                        echo 'length <= 152: true<br>';
                                    }
                                    else {
                                        echo 'length <= 152: false<br>';
                                        $isSpam = true;
                                    }
                                }
                            }
                            else {
                                echo 'excitation <= 0: false<br>';
                                $isSpam = true;
                            }
                        }
                        else {
                            echo 'length <= 163: false, length is ' . $lengthOfText . '<br>';
                        }
                    }
                }
            }
            else {
                echo 'doesHaveUnusualSymbol: true<br>';
                if ($doesHaveLinks == false) {
                    echo 'doesHaveLinks: false<br>';
                    if ($doesHaveSpammyWords == false) {
                        echo 'doesHaveSpammyWords: false<br>';
                    }
                    else {
                        echo 'doesHaveSpammyWords: true<br>';
                        if ($lengthOfText <= 187) {
                            echo 'length <= 187: true, length is ' . $lengthOfText . '<br>';
                            if ($excitation <= 0) {
                                if ($lengthOfText <= 149) {
                                    echo 'length <= 149: true, length is ' . $lengthOfText . '<br>';
                                }
                                else {
                                    echo 'length <= 149: false, length is ' . $lengthOfText . '<br>';
                                    $isSpam = true;
                                }
                            }
                            else {
                                echo 'excitation <= 0: false<br>';
                                $isSpam = true;
                            }
                        }
                        else {
                            echo 'length <= 187: false, length is ' . $lengthOfText . '<br>';
                        }
                    }
                }
                else {
                    echo 'doesHaveLinks: true<br>';
                    $isSpam = true;
                }
            }
        }
        else {
            echo 'doesHavePhoneNumber: true<br>';
            $isSpam = true;
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
        $excitation = get_excitation($text_message);
        $doesHavePhoneNumber = get_doesHavePhoneNumber($text_message);
        $doesHaveUnusualSymbol = get_doesHaveUnusualSymbol($text_message);
        
        $isSpam = make_prediction($doesHaveLinks, $doesHaveSpammyWords, $lengthOfText,
        $excitation, $doesHavePhoneNumber, $doesHaveUnusualSymbol);

        output($isSpam);
    }

    main();
?>