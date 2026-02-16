<?php
include 'auth_check.php';
$stmt = $pdo->query("SELECT * FROM surveys ORDER BY id DESC");
echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
}

Future<Map<String, dynamic>> deleteSurvey(String token, String id) async {
  // Pastikan URL mengarah ke survey_list.php?del=ID
  final url = Uri.parse("${AppConfig.baseUrl}/survey_list.php?del=$id");
  final res = await http.get(url, headers: {'X-API-KEY': token});
  return json.decode(res.body);
}
