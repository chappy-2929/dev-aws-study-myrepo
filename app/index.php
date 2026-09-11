<?php
// ==============================================================================
// 🎮 参加者カスタマイズ領域（ここを自由に変更してください）
// ==============================================================================
$PROFILE = [
    // プレイヤー名・ロール名
    'player_name'  => 'CHAPPY-2929',
    'badge_role'   => 'Cloud Infrastructure Engineer',
    'status_badge' => 'SYSTEM ONLINE',
    
    // アバター画像URL（GitHubのアイコンや、Unsplash、任意の画像URLを指定可能）
    'avatar_url'   => 'https://github.com/chappy-2929.png',
    
    // 自己紹介 / 達成コメント
    'bio'          => "Terraform × ECS × GitHub Actions OIDC 完全制覇！\nインフラからパイプラインまで一気通貫で自動化環境を構築しました。",
    
    // 6角形レーダーチャートのパラメータ（各項目 0〜100）
    'parameters'   => [
        'IaC (Terraform)'    => 90,
        'AWS Architecture'   => 85,
        'Docker / Container' => 80,
        'CI/CD Pipeline'     => 95,
        'Security & IAM'     => 90,
        'Troubleshooting'    => 100, // 泥臭い切り分け突破力！
    ],
    
    // ランタイム表示テキスト
    'environment'  => 'AWS ECS Fargate (ap-northeast-1)',
    'db_engine'    => 'Amazon Aurora / RDS MySQL',
];

// ヘルスチェック用（ALBからのステータス確認に対して200 OKを返却）
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'status.php') !== false) {
    http_response_code(200);
    echo "OK";
    exit;
}

$chart_labels = json_encode(array_keys($PROFILE['parameters']), JSON_UNESCAPED_UNICODE);
$chart_values = json_encode(array_values($PROFILE['parameters']));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($PROFILE['player_name']) ?> | Cloud Architect Status</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #0b0f17;
            color: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .cyber-card {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(56, 189, 248, 0.2);
            backdrop-filter: blur(12px);
            border-radius: 1rem;
        }
        .cyber-glow {
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">

    <div class="max-w-4xl w-full space-y-6">
        
        <!-- Header Card: Profile & Bio -->
        <div class="cyber-card cyber-glow p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Avatar Image -->
                <div class="relative group">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border-2 border-cyan-400 p-1 bg-slate-900 shadow-lg">
                        <img src="<?= htmlspecialchars($PROFILE['avatar_url']) ?>" 
                             alt="Avatar" 
                             class="w-full h-full object-cover rounded-xl"
                             onerror="this.src='https://api.dicebear.com/7.x/bottts/svg?seed=fallback'">
                    </div>
                    <span class="absolute -bottom-2 -right-2 bg-emerald-500 text-slate-950 font-bold text-xs px-2 py-0.5 rounded-full border-2 border-slate-900">
                        ONLINE
                    </span>
                </div>

                <!-- Text Info -->
                <div class="flex-1 text-center sm:text-left space-y-2">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-950 text-emerald-400 border border-emerald-500/30">
                            <?= htmlspecialchars($PROFILE['status_badge']) ?>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-950 text-cyan-400 border border-cyan-500/30">
                            <?= htmlspecialchars($PROFILE['badge_role']) ?>
                        </span>
                    </div>

                    <h1 class="text-3xl font-extrabold tracking-tight text-white">
                        <?= htmlspecialchars($PROFILE['player_name']) ?>
                    </h1>

                    <p class="text-slate-400 text-sm leading-relaxed whitespace-pre-line">
<?= htmlspecialchars($PROFILE['bio']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard: Hexagon Radar Chart & Telemetry -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Radar Chart Card -->
            <div class="cyber-card p-6 flex flex-col items-center justify-center">
                <div class="w-full flex justify-between items-center mb-4">
                    <h2 class="text-cyan-400 font-bold tracking-wider text-sm uppercase">Capability Radar</h2>
                    <span class="text-xs text-slate-500">HEXAGON MATRIX</span>
                </div>
                <div class="w-full max-w-[340px] aspect-square flex items-center justify-center">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>

            <!-- Telemetry & Specs -->
            <div class="cyber-card p-6 flex flex-col justify-between space-y-6">
                <div>
                    <h2 class="text-cyan-400 font-bold tracking-wider text-sm uppercase mb-4">Runtime Telemetry</h2>
                    <div class="space-y-3">
                        <div class="bg-slate-900/60 p-3 rounded-lg border border-slate-800 flex justify-between items-center">
                            <span class="text-xs text-slate-400">Compute Platform</span>
                            <span class="text-xs font-mono text-emerald-400"><?= htmlspecialchars($PROFILE['environment']) ?></span>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-lg border border-slate-800 flex justify-between items-center">
                            <span class="text-xs text-slate-400">Database Engine</span>
                            <span class="text-xs font-mono text-cyan-400"><?= htmlspecialchars($PROFILE['db_engine']) ?></span>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-lg border border-slate-800 flex justify-between items-center">
                            <span class="text-xs text-slate-400">Deployment Pipeline</span>
                            <span class="text-xs font-mono text-indigo-400">GitHub Actions OIDC</span>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-lg border border-slate-800 flex justify-between items-center">
                            <span class="text-xs text-slate-400">Security / Identity</span>
                            <span class="text-xs font-mono text-yellow-400">AWS IAM AssumeRole</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-800 pt-4 flex items-center justify-between text-xs text-slate-500 font-mono">
                    <span>STATUS: DEPLOYED</span>
                    <span>AWS REGION: ap-northeast-1</span>
                </div>
            </div>

        </div>

    </div>

    <!-- Chart Configuration Script -->
    <script>
        const ctx = document.getElementById('radarChart').getContext('2d');
        const radarLabels = <?= $chart_labels ?>;
        const radarValues = <?= $chart_values ?>;

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: radarLabels,
                datasets: [{
                    label: 'Status Level',
                    data: radarValues,
                    backgroundColor: 'rgba(56, 189, 248, 0.25)',
                    borderColor: '#38bdf8',
                    borderWidth: 2,
                    pointBackgroundColor: '#38bdf8',
                    pointBorderColor: '#ffffff',
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: '#38bdf8',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        angleLines: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        pointLabels: {
                            color: '#94a3b8',
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        },
                        ticks: {
                            display: false,
                            stepSize: 20
                        },
                        min: 0,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>
</html>