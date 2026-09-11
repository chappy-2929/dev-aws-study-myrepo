<?php
// ==============================================================================
// 1. ALB ヘルスチェック専用応答（最優先）
// ==============================================================================
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($uri, 'status.php') !== false) {
    http_response_code(200);
    header('Content-Type: text/plain; charset=utf-8');
    echo "OK";
    exit;
}

// ==============================================================================
// 🎮 参加者カスタマイズ領域（ここを自由に変更してください）
// ==============================================================================
$PROFILE = [
    // プレイヤー基本情報
    'player_name'  => 'CHAPPY-2929',
    'badge_role'   => 'Cloud Infrastructure Engineer',
    'status_badge' => 'OPERATIONAL 100%',
    
    // アバター画像URL
    'avatar_url'   => 'https://github.com/chappy-2929.png',
    
    // 自己紹介 / 実績コメント
    'bio'          => "Terraform × ECS Fargate × GitHub Actions OIDC 完全制覇！\n強固なIAMポリシーと自動化パイプラインで、ゼロタッチデプロイ環境を完備しました。",
    
    // 6角形レーダーチャートのパラメータ（各項目 0〜100）
    'parameters'   => [
        'IaC (Terraform)'    => 90,
        'AWS Architecture'   => 85,
        'Docker / Container' => 80,
        'CI/CD Pipeline'     => 95,
        'Security & IAM'     => 90,
        'Troubleshooting'    => 100, // 粘り勝ちの切り分け力！
    ],
    
    // ランタイム表示スペック
    'environment'  => 'AWS ECS Fargate (ap-northeast-1)',
    'db_engine'    => 'Amazon RDS MySQL',
    'load_balancer'=> 'Application Load Balancer (Active)',
];

$chart_labels = json_encode(array_keys($PROFILE['parameters']), JSON_UNESCAPED_UNICODE);
$chart_values = json_encode(array_values($PROFILE['parameters']));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($PROFILE['player_name']); ?> | Cloud Telemetry Hub</title>
    
    <!-- Cyber Hexagon Favicon (SVG Data URI) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><polygon points='50 3, 90 25, 90 75, 50 97, 10 75, 10 25' fill='%230b0f17' stroke='%2338bdf8' stroke-width='6'/><polygon points='50 20, 75 35, 75 65, 50 80, 25 65, 25 35' fill='%2338bdf8' opacity='0.8'/></svg>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background-color: #050811;
            color: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden;
        }

        /* 背景：サイバーグリッドメッシュ */
        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: 
                linear-gradient(to right, rgba(56, 189, 248, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(56, 189, 248, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* 背景：オーロラ光彩（立体感の演出） */
        .ambient-glow-1 {
            position: fixed;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            filter: blur(60px);
        }
        .ambient-glow-2 {
            position: fixed;
            bottom: -10%;
            right: -10%;
            width: 55vw;
            height: 55vw;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            filter: blur(70px);
        }

        /* グラスモーフィズムカード */
        .glass-card {
            background: rgba(13, 20, 36, 0.7);
            border: 1px solid rgba(56, 189, 248, 0.22);
            backdrop-filter: blur(16px);
            border-radius: 1.25rem;
            box-shadow: 0 10px 35px -5px rgba(0, 0, 0, 0.5), 0 0 20px rgba(56, 189, 248, 0.1);
        }

        /* 生存パルスアニメーション */
        @keyframes radar-pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; filter: drop-shadow(0 0 8px #10b981); }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .pulse-alive {
            animation: radar-pulse 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8 relative">

    <!-- 背景エフェクト -->
    <div class="cyber-grid"></div>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="max-w-4xl w-full space-y-6 relative z-10">
        
        <!-- ヘッダープロファイルカード -->
        <div class="glass-card p-6 sm:p-8 transition-all duration-300 hover:border-cyan-400/40">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                
                <!-- アバター枠（ネオンボーダー） -->
                <div class="relative group">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border-2 border-cyan-400/80 p-1 bg-slate-950/80 shadow-[0_0_15px_rgba(56,189,248,0.3)]">
                        <img src="<?php echo htmlspecialchars($PROFILE['avatar_url']); ?>" 
                             alt="Avatar" 
                             class="w-full h-full object-cover rounded-xl"
                             onerror="this.src='https://api.dicebear.com/7.x/bottts/svg?seed=fallback'">
                    </div>
                    <!-- パルスLEDインジケータ -->
                    <div class="absolute -bottom-2 -right-2 flex items-center gap-1 bg-slate-900 border border-emerald-500/50 px-2 py-0.5 rounded-full shadow-lg">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-alive"></span>
                        <span class="text-[10px] font-mono font-bold text-emerald-400">ACTIVE</span>
                    </div>
                </div>

                <!-- ユーザー情報 -->
                <div class="flex-1 text-center sm:text-left space-y-3">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="px-3 py-0.5 rounded-full text-xs font-mono font-semibold bg-emerald-950/80 text-emerald-400 border border-emerald-500/40 shadow-[0_0_10px_rgba(16,185,129,0.15)]">
                            ● <?php echo htmlspecialchars($PROFILE['status_badge']); ?>
                        </span>
                        <span class="px-3 py-0.5 rounded-full text-xs font-semibold bg-cyan-950/80 text-cyan-300 border border-cyan-500/40 shadow-[0_0_10px_rgba(56,189,248,0.15)]">
                            <?php echo htmlspecialchars($PROFILE['badge_role']); ?>
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white font-mono flex items-center justify-center sm:justify-start gap-2">
                        <span><?php echo htmlspecialchars($PROFILE['player_name']); ?></span>
                        <span class="text-xs text-cyan-400/60 font-normal px-2 py-0.5 rounded border border-cyan-400/20">AWS-PRO</span>
                    </h1>

                    <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line font-sans">
<?php echo htmlspecialchars($PROFILE['bio']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- メインパネル：レーダーチャート & テレメトリ -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- 6角形レーダーチャート -->
            <div class="glass-card p-6 flex flex-col items-center justify-between">
                <div class="w-full flex justify-between items-center mb-2">
                    <h2 class="text-cyan-400 font-bold tracking-wider text-xs uppercase font-mono flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full"></span>
                        Capability Radar
                    </h2>
                    <span class="text-[10px] font-mono text-cyan-400/50 bg-cyan-950/50 px-2 py-0.5 rounded border border-cyan-500/20">HEXAGON v2.4</span>
                </div>

                <div class="w-full max-w-[320px] aspect-square flex items-center justify-center">
                    <canvas id="radarChart"></canvas>
                </div>

                <div class="w-full text-center mt-2">
                    <span class="text-[11px] font-mono text-slate-400">OVERALL ARCHITECT SCORE: <strong class="text-cyan-300 font-bold">92.5 pt</strong></span>
                </div>
            </div>

            <!-- リアルタイム・テレメトリ（動くギミック） -->
            <div class="glass-card p-6 flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-cyan-400 font-bold tracking-wider text-xs uppercase font-mono flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full"></span>
                            Live System Telemetry
                        </h2>
                        <span class="text-[10px] font-mono text-emerald-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-alive"></span>
                            STREAMING
                        </span>
                    </div>

                    <div class="space-y-2.5">
                        <div class="bg-slate-950/60 p-2.5 rounded-xl border border-slate-800/80 flex justify-between items-center">
                            <span class="text-xs text-slate-400 font-mono">Platform</span>
                            <span class="text-xs font-mono text-emerald-400 font-semibold"><?php echo htmlspecialchars($PROFILE['environment']); ?></span>
                        </div>
                        <div class="bg-slate-950/60 p-2.5 rounded-xl border border-slate-800/80 flex justify-between items-center">
                            <span class="text-xs text-slate-400 font-mono">Routing & Balancing</span>
                            <span class="text-xs font-mono text-cyan-400 font-semibold"><?php echo htmlspecialchars($PROFILE['load_balancer']); ?></span>
                        </div>
                        <div class="bg-slate-950/60 p-2.5 rounded-xl border border-slate-800/80 flex justify-between items-center">
                            <span class="text-xs text-slate-400 font-mono">Database</span>
                            <span class="text-xs font-mono text-purple-400 font-semibold"><?php echo htmlspecialchars($PROFILE['db_engine']); ?></span>
                        </div>
                        <div class="bg-slate-950/60 p-2.5 rounded-xl border border-slate-800/80 flex justify-between items-center">
                            <span class="text-xs text-slate-400 font-mono">CI/CD Auth</span>
                            <span class="text-xs font-mono text-yellow-400 font-semibold">GitHub OIDC (Zero-Secret)</span>
                        </div>

                        <!-- リアルタイム稼働カウンター（生きている演出） -->
                        <div class="bg-slate-950/60 p-2.5 rounded-xl border border-cyan-500/20 flex justify-between items-center shadow-[inset_0_0_10px_rgba(56,189,248,0.05)]">
                            <span class="text-xs text-slate-400 font-mono">Session Uptime</span>
                            <span id="session-counter" class="text-xs font-mono text-cyan-300 font-bold">00:00:00</span>
                        </div>
                    </div>
                </div>

                <!-- フッターメタデータ -->
                <div class="border-t border-slate-800/80 pt-3 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                        REGION: ap-northeast-1
                    </span>
                    <span>HTTPS / TLSv1.3</span>
                </div>
            </div>

        </div>

    </div>

    <!-- 演出用スクリプト -->
    <script>
        // 1. レーダーチャートのアニメーション設定
        const ctx = document.getElementById('radarChart').getContext('2d');
        const radarLabels = <?php echo $chart_labels; ?>;
        const radarValues = <?php echo $chart_values; ?>;

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: radarLabels,
                datasets: [{
                    data: radarValues,
                    backgroundColor: 'rgba(56, 189, 248, 0.28)',
                    borderColor: '#38bdf8',
                    borderWidth: 2,
                    pointBackgroundColor: '#38bdf8',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: '#38bdf8',
                    pointRadius: 4.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 1600,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#38bdf8',
                        borderColor: 'rgba(56, 189, 248, 0.3)',
                        borderWidth: 1,
                        padding: 10,
                        bodyFont: { family: 'monospace' }
                    }
                },
                scales: {
                    r: {
                        angleLines: { color: 'rgba(56, 189, 248, 0.15)' },
                        grid: { color: 'rgba(56, 189, 248, 0.1)' },
                        pointLabels: {
                            color: '#94a3b8',
                            font: { size: 11, weight: '600', family: 'sans-serif' }
                        },
                        ticks: { display: false, stepSize: 20 },
                        min: 0,
                        max: 100
                    }
                }
            }
        });

        // 2. 稼働タイマー（Uptime）ギミック
        let seconds = 0;
        const timerElement = document.getElementById('session-counter');
        setInterval(() => {
            seconds++;
            const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const secs = String(seconds % 60).padStart(2, '0');
            timerElement.textContent = `${hrs}:${mins}:${secs}`;
        }, 1000);
    </script>
</body>
</html>