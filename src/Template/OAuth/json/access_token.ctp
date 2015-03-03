<?php
if ($response instanceof \League\OAuth2\Server\Exception\OAuthException) {
    echo json_encode([
        'error' => $response->errorType,
        'message' => $response->getMessage()
    ]);
} else {
    echo json_encode($response);
}
