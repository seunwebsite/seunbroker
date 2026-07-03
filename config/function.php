<?php

/**
 * EMAIL FUNCTION (SAFE + DEBUG READY)
 */
/**
 * EMAIL FUNCTION (HARDCODED API KEY)
 */
function sendMail($email, $subject, $message) {

    // Ensure your key is secure. 
    // Replace the string below with your NEW key after you delete the old one.
    $key = getenv('RESEND_API_KEY') ?: 're_YOUR_NEW_API_KEY_HERE';

    try {
        $resend = \Resend::client($key);

        $resend->emails->send([
            'from'    => 'HostHeritage <onboarding@mail.hostheritage.com>', 
            'to'      => [$email],
            'subject' => $subject,
            'html'    => $message,
        ]);

        return true;

    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage()
        ];
    }
}

function sendSupportMail($email, $subject, $message, $replyTo) {
    $key = getenv('RESEND_API_KEY') ?: 're_YOUR_NEW_API_KEY_HERE';

    try {
        $resend = \Resend::client($key);

        $resend->emails->send([
            // CHANGE THIS LINE:
            'from'     => 'Support <support@yourdomain.com>', 
            'to'       => [$email],
            'subject'  => $subject,
            'html'     => $message,
            'reply_to' => $replyTo, 
        ]);

        return true;
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
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