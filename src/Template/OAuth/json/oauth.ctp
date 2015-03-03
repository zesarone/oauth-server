<?php
echo json_encode([
    'error' => $e->errorType,
    'message' => $e->getMessage()
]);