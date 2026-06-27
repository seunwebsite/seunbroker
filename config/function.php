<?php

/**
 * EMAIL FUNCTION (SAFE + DEBUG READY)
 */
/**
 * EMAIL FUNCTION (HARDCODED API KEY)
 */
function sendMail($email, $subject, $message) {

    // Hardcoded API Key
    $key = 're_GkMAQB3u_7FwRQXQSaarXQ4zzPDy2LtPo';

    try {
        // Initialize Resend with the hardcoded key
        $resend = \Resend::client($key);

        $resend->emails->send([
            'from' => 'mytradingaxis <mail@mytradingaxis.live>',
            'to' => [$email],
            'subject' => $subject,
            'html' => $message,
        ]);

        return true;

    } catch (\Exception $e) {
        // Return the error message if something goes wrong
        return [
            'error' => $e->getMessage()
        ];
    }
}

/**
 * CLEAN INPUT
 */
function clean($data) {
    global $link;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($link, $data);
}

/**
 * REDIRECT
 */
function redirect($url) {
    echo "<script>window.location.href='$url';</script>";
    exit;
}

/**
 * WALLET PHRASE
 */
function generateWalletPhrase() {
    $wordList = [
        "quick","giraffe","ethereal","verdant","mellifluous","elephant","effervescent","enigma",
        "banana","paradox","quixotic","shiny","alpha","bravo","delta","echo","foxtrot","golf",
        "hotel","india","juliet","kilo","lima","mike","november","oscar","papa","quebec",
        "romeo","sierra","tango","uniform","victor","whiskey","xray","yankee","zulu","orbit",
        "galaxy","comet","planet","star","nebula","cosmos","solar","lunar","asteroid","meteor",
        "crypto","chain","block","token","asset","value","trade","market","secure","key"
    ];

    shuffle($wordList);
    return implode(" ", array_slice($wordList, 0, 12));
}