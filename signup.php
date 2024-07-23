<?php
include ('config.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
       header ("Location:thankyou.html");
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptchaSecret = '6LeNcvIpAAAAADVrvRDP5tNkwCsE4lNs2Fvx9B2r';

    // Check if reCAPTCHA response is set
    if (isset($_POST['g-recaptcha-response'])) {
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        // Verify the reCAPTCHA response with Google
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $recaptchaSecret,
            'response' => $recaptchaResponse
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $result = json_decode($response, true);

        if ($result['success']) {
            // reCAPTCHA was successfully validated
            // Process the registration form
            echo "Registration successful!";
        } else {
            // reCAPTCHA validation failed
            echo 'reCAPTCHA verification failed. Please try again.';
        }
    } else {
        echo 'reCAPTCHA response is missing.';
    }
}
?>