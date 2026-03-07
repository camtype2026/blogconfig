<?php
/**
 * BlogPilot Config Server
 * 경로: /public_html/api/config.php
 * 
 * 앱이 초대 코드로 요청 → Webhook URL + 버전 정보 응답
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ── 설정 (운영자가 수정하는 부분) ─────────────────────────────────────────

$WEBHOOK_URL    = 'https://your-n8n.app/webhook/dman'; // n8n URL 바뀌면 여기만 수정
$LATEST_VERSION = '1.0.0';                              // 새 버전 배포 시 올림
$APK_URL        = 'https://blogpilot.kr/apk/blogpilot_latest.apk'; // APK 파일 URL

// ── 초대 코드 목록 ────────────────────────────────────────────────────────
// code => grade (free/pro/vip)
$INVITE_CODES = [
    'BLOGPILOT' => 'free',
    'BLOG-PRO'  => 'pro',
    'BLOG-VIP'  => 'vip',
];

// ── 요청 처리 ─────────────────────────────────────────────────────────────

$input = json_decode(file_get_contents('php://input'), true);
$code  = strtoupper(trim($input['invite_code'] ?? ''));
$appVersion = trim($input['app_version'] ?? '0.0.0');

if (empty($code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '초대 코드를 입력하세요']);
    exit;
}

if (!isset($INVITE_CODES[$code])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '유효하지 않은 초대 코드입니다']);
    exit;
}

$grade = $INVITE_CODES[$code];

// 버전 비교
$hasUpdate = version_compare($appVersion, $LATEST_VERSION, '<');

echo json_encode([
    'success'        => true,
    'webhook_url'    => $WEBHOOK_URL,
    'grade'          => $grade,          // free / pro / vip
    'latest_version' => $LATEST_VERSION,
    'has_update'     => $hasUpdate,
    'apk_url'        => $hasUpdate ? $APK_URL : null,
    'message'        => '인증 완료',
]);
