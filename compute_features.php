<?php
    // --------------------------------------------------------------------------------
    // three functions for determining features
    function get_lengthOfText($mailText) {
        return strlen($mailText);
    }

    function get_doesHaveLinks($mailText) {
        if (stripos($mailText, 'http') != false)
            return true;
        if (stripos($mailText, 'www') != false)
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
    // --------------------------------------------------------------------------------
    // three functions for the three features for the extra credit
    
    // feature 4: excitation.
    // explanation: Generally, spam messages look much exciting than normal non-spam message.
    // measure this feature with the number of '!' in the message.
    function get_excitation($mailText) {
        $excitation = substr_count($mailText, '!');
        return $excitation;
    }

    // feature 5: doesHavePhoneNumber
    // explanation: Obviously, a spam message is much more likely to contain a phone number
    function get_doesHavePhoneNumber($mailText) {
        $pattern = '/\d{11}|\d{4}[\s]\d{3}[\s]\d{4}|\d{4}[\-]\d{3}[\-]\d{4}/';
        if (preg_match($pattern, $mailText) != false)
            return true;
        return false;
    }

    // feature 6: doesHaveUnusualSymbol
    // explanation: Such symbols (<, >, +, $, /, -, &) are much more likely to appear
    // in a spam message than a normal message. People seldom use them in daily life.
    function get_doesHaveUnusualSymbol($mailText) {
        $unusualWords = array('<', '>', '+', '$', '/', '-', '&');
        for ($i = 0; $i < sizeof($unusualWords); $i++) {
            $unusualWord = $unusualWords[$i];
            if (stripos($mailText, $unusualWord) != false)
                return true;
        }
        return false;
    }

    // --------------------------------------------------------------------------------
    // the function for writting features into features.csv
    function writeToCSV($mailInfos) {
        $stream = fopen('features.csv', 'w');
        // write header
        $headers = ['doesHaveLinks', 'doesHaveSpammyWords', 'lengthOfText', 
        'excitation', 'doesHavePhoneNumber', 'doesHaveUnusualSymbol', 'classLabel'];
        fputcsv($stream, $headers);
        // write each row
        foreach ($mailInfos as $fields) {
            fputcsv($stream, $fields);
        }
        fclose($stream);
    }
    // --------------------------------------------------------------------------------
    // Connect to the databse
    function main() {
        $db_host = 'localhost';
        $db_user = 'root';
        $db_password = 'root';
        $db_db = 'predict_spam';
        $db_port = 3307;
        $conn = new mysqli($db_host, $db_user,$db_password, $db_db, $db_port);
        // --------------------------------------------------------------------------------
        // Take all information from the database
        $sql = "SELECT * FROM spam";
        $result = $conn->query($sql);
        $numResults = $result->num_rows;
        if ($numResults > 0)
        {
            $spamData = array();
            while ($row = $result->fetch_assoc())
            {
                $spamData[] = $row;
            }
            $result = ['data' => $spamData, 'totalNumberOfRecords' => $numResults, 'errors' => [], 'responseCode' => 200];
        }else
        {
            echo "error";
        }
        // --------------------------------------------------------------------------------
        // Scan every email's text and compute features
        $mails = $result['data'];
        $mailInfos = array();
        for ($i = 0; $i < sizeof($mails); $i++) {
            $mailText = $mails[$i]['v2'];
            // get feature: classLabel
            $classLabel = $mails[$i]['v1'];
            // get feature: lengthOfText
            $lengthOfText = get_lengthOfText($mailText);
            // get feature: doesHaveLinks
            $doesHaveLinks = get_doesHaveLinks($mailText);
            // get feature: doesHaveSpammyWords (free, award, !, +, call, urgent, txt, win, won, congrat), case insensitive
            $doesHaveSpammyWords = get_doesHaveSpammyWords($mailText);

            // Additional three features for the extra credit
            // explanations see the comments besides the getter functions
            $excitation = get_excitation($mailText);
            $doesHavePhoneNumber = get_doesHavePhoneNumber($mailText);
            $doesHaveUnusualSymbol = get_doesHaveUnusualSymbol($mailText);

            // store this email's information
            $mailInfo = array(
                'doesHaveLinks' => json_encode($doesHaveLinks),
                'doesHaveSpammyWords' => json_encode($doesHaveSpammyWords),
                'lengthOfText' => $lengthOfText,
                'excitation' => $excitation,
                'doesHavePhoneNumber' => json_encode($doesHavePhoneNumber),
                'doesHaveUnusualSymbol' => json_encode($doesHaveUnusualSymbol),
                'classLabel' => $classLabel
            );
            $mailInfos[] = $mailInfo;
        }
        // --------------------------------------------------------------------------------
        // Write these 4 features into features.csv
        writeToCSV($mailInfos);
        // --------------------------------------------------------------------------------
        // Close connection
        mysqli_close($conn);
    }
    // --------------------------------------------------------------------------------

    main();
?>