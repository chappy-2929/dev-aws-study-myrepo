<?php
$hostname = gethostname();
$server_ip = $_SERVER['SERVER_ADDR'] ?? 'Unknown';
$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cloud Architect Status Board</title>
  <style>
    :root {
      --bg: #0d1117;
      --card-bg: #161b22;
      --border: #30363d;
      --accent: #58a6ff;
      --green: #3fb950;
      --purple: #bc8cff;
      --text: #c9d1d9;
      --text-muted: #8b949e;
    }
    body {
      margin: 0;
      padding: 2rem 1rem;
      background-color: var(--bg);
      color: var(--text);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
      display: flex;
      justify-content: center;
    }
    .container {
      max-width: 800px;
      width: 100%;
    }
    .badge {
      display: inline-block;
      padding: 0.2rem 0.6rem;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: bold;
      margin-right: 0.5rem;
    }
    .badge-fargate { background: #232f3e; color: #ff9900; border: 1px solid #ff9900; }
    .badge-live { background: rgba(63, 185, 80, 0.2); color: var(--green); border: 1px solid var(--green); }
    
    .card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    h1 { margin-top: 0; color: #fff; font-size: 1.8rem; }
    h2 { color: var(--accent); font-size: 1.2rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-top: 0; }
    
    .status-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .stat-box {
      background: rgba(255, 255, 255, 0.03);
      padding: 1rem;
      border-radius: 6px;
      border-left: 3px solid var(--accent);
    }
    .stat-label { font-size: 0.8rem; color: var(--text-muted); }
    .stat-val { font-size: 1.1rem; font-weight: bold; color: #fff; margin-top: 0.2rem; }

    .param-bar { margin-bottom: 0.8rem; }
    .param-header { display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.3rem; }
    .progress-bg { background: #21262d; border-radius: 4px; height: 8px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 4px; }
    .fill-blue { background: var(--accent); }
    .fill-green { background: var(--green); }
    .fill-purple { background: var(--purple); }

    .env-info {
      font-family: monospace;
      font-size: 0.85rem;
      background: #000;
      padding: 1rem;
      border-radius: 6px;
      line-height: 1.6;
      color: #79c0ff;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
        <div>
          <span class="badge badge-live">● SYSTEM ONLINE</span>
          <span class="badge badge-fargate">AWS Fargate</span>
          <h1>Cloud Architect Status</h1>
        </div>
      </div>
      <p style="color: var(--text-muted);">Terraform × ECS × WordPress ハンズオン完了記念ボード</p>
    </div>

    <!-- ステータスパラメータ -->
    <div class="card">
      <h2>PLAYER PARAMETERS</h2>
      <div class="param-bar">
        <div class="param-header"><span>Terraform (IaC 構築力)</span><span>Lv.4</span></div>
        <div class="progress-bg"><div class="progress-fill fill-blue" style="width: 80%;"></div></div>
      </div>
      <div class="param-bar">
        <div class="param-header"><span>AWS Architecture (設計力)</span><span>Lv.4</span></div>
        <div class="progress-bg"><div class="progress-fill fill-green" style="width: 75%;"></div></div>
      </div>
      <div class="param-bar">
        <div class="param-header"><span>Docker / Container (コンテナ運用力)</span><span>Lv.3</span></div>
        <div class="progress-bg"><div class="progress-fill fill-purple" style="width: 60%;"></div></div>
      </div>
      <div class="param-bar">
        <div class="param-header"><span>CI/CD Pipeline (自動化力)</span><span>Lv.3</span></div>
        <div class="progress-bg"><div class="progress-fill fill-blue" style="width: 65%;"></div></div>
      </div>
    </div>

    <!-- コンテナ / インフラ稼働情報 -->
    <div class="card">
      <h2>RUNTIME TELEMETRY</h2>
      <div class="status-grid">
        <div class="stat-box">
          <div class="stat-label">AZ CONFIGURATION</div>
          <div class="stat-val">Multi-AZ (1a / 1c)</div>
        </div>
        <div class="stat-box">
          <div class="stat-label">DATABASE</div>
          <div class="stat-val">RDS MySQL 8.0</div>
        </div>
        <div class="stat-box">
          <div class="stat-label">STORAGE</div>
          <div class="stat-val">EFS Mount Validated</div>
        </div>
      </div>

      <div class="env-info">
        > Container Hostname : <?= htmlspecialchars($hostname) ?><br>
        > Fargate IP Address : <?= htmlspecialchars($server_ip) ?><br>
        > Remote Client IP   : <?= htmlspecialchars($client_ip) ?><br>
        > Status             : 200 OK (SSL/TLS Terminated at ALB)
      </div>
    </div>
  </div>
</body>
</html>