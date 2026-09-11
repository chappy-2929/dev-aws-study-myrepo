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
// 🎮 参加者カスタマイズ領域
// ==============================================================================
$PROFILE = [
    'player_name'  => 'CHAPPY-2929',
    'badge_role'   => 'Cloud Engineer',
    'status_badge' => 'All AWS Certifications Engineer',
    
    // アバター画像URL
    'avatar_url'   => 'https://github.com/chappy-2929.png',
    
    // 自己紹介 / 達成コメント
    'bio'          => "Terraform × ECS Fargate × GitHub Actions OIDC 完全制覇！\n強固なIAM信頼ポリシーと完全自動化パイプラインで、ゼロタッチデプロイ環境を完備。",
    
    // 6角形レーダーチャート（文字長を考慮した短縮表記で綺麗に収めます）
    'parameters'   => [
        'IaC (Terraform)'   => 92,
        'AWS Architecture'  => 88,
        'Docker Container'  => 70,
        'CI/CD Automation'  => 75,
        'IAM & Security'    => 90,
        'Troubleshooting'   => 80, // 不動のフルスペック
    ],
    
    // テレメトリスペック
    'environment'  => 'AWS ECS Fargate',
    'db_engine'    => 'Amazon RDS MySQL',
    'load_balancer'=> 'Application Load Balancer',
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
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><polygon points='50 3, 90 25, 90 75, 50 97, 10 75, 10 25' fill='%23050b14' stroke='%2338bdf8' stroke-width='6'/><polygon points='50 20, 75 35, 75 65, 50 80, 25 65, 25 35' fill='%2338bdf8'/></svg>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background-color: #030712;
            color: #f1f5f9;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            overflow-x: hidden;
        }

        /* 浮遊パーティクル用 Canvas */
        #bg-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        /* 超高精細サイバーグリッド */
        .cyber-grid {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(56, 189, 248, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(56, 189, 248, 0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 1;
        }

        /* グラスカードとネオン境界線 */
        .cyber-glass {
            background: rgba(11, 19, 38, 0.75);
            border: 1px solid rgba(56, 189, 248, 0.25);
            backdrop-filter: blur(20px);
            border-radius: 1.25rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7), 0 0 25px rgba(56, 189, 248, 0.08);
            position: relative;
            overflow: hidden;
        }

        /* カード上部の走査レーザー光 */
        .cyber-glass::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #38bdf8, transparent);
            animation: scanline 4s infinite linear;
        }

        @keyframes scanline {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* ヘキサゴン・アバタークリップ */
        .clip-hex {
            clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
        }

        /* 回転するアバタースキャナーリング */
        @keyframes rotate-ring {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .scanner-ring {
            animation: rotate-ring 12s linear infinite;
        }

        /* ターミナル風スクロールテキスト */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(56, 189, 248, 0.3);
            border-radius: 2px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-3 sm:p-6 relative">

    <canvas id="bg-canvas"></canvas>
    <div class="cyber-grid"></div>

    <div class="max-w-4xl w-full space-y-5 relative z-10 my-4">
        
        <!-- ヘッダーカード -->
        <div class="cyber-glass p-6 sm:p-7">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                
                <!-- ヘキサゴン・アバターHUD -->
                <div class="relative flex items-center justify-center">
                    <div class="w-28 h-28 absolute border border-cyan-500/40 rounded-full border-dashed scanner-ring"></div>
                    <div class="w-24 h-24 clip-hex bg-cyan-400 p-[2px] shadow-[0_0_20px_rgba(56,189,248,0.4)]">
                        <div class="w-full h-full clip-hex bg-slate-950 overflow-hidden flex items-center justify-center">
                            <img src="<?php echo htmlspecialchars($PROFILE['avatar_url']); ?>" 
                                 alt="Avatar" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://api.dicebear.com/7.x/bottts/svg?seed=fallback'">
                        </div>
                    </div>
                    <div class="absolute -bottom-1 bg-emerald-500 text-slate-950 font-black text-[9px] px-2 py-0.5 rounded-full border border-slate-900 shadow">
                        ONLINE
                    </div>
                </div>

                <!-- プロファイル基本情報 -->
                <div class="flex-1 text-center sm:text-left space-y-2.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-950/90 text-emerald-400 border border-emerald-500/40 flex items-center gap-1.5 shadow-[0_0_10px_rgba(16,185,129,0.2)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <?php echo htmlspecialchars($PROFILE['status_badge']); ?>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-cyan-950/90 text-cyan-300 border border-cyan-500/40 shadow-[0_0_10px_rgba(56,189,248,0.2)]">
                            <?php echo htmlspecialchars($PROFILE['badge_role']); ?>
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white flex items-center justify-center sm:justify-start gap-3">
                        <span class="bg-gradient-to-r from-cyan-400 via-sky-300 to-indigo-300 bg-clip-text text-transparent"><?php echo htmlspecialchars($PROFILE['player_name']); ?></span>
                        <span class="text-[10px] tracking-widest text-cyan-400 border border-cyan-400/40 px-1.5 py-0.5 rounded bg-cyan-950/40">NODE #01</span>
                    </h1>

                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed whitespace-pre-line font-sans opacity-90">
<?php echo htmlspecialchars($PROFILE['bio']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- コアダッシュボード：レーダー ＆ テレメトリ -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
            
            <!-- レーダーチャート（横幅を広めに確保して文字欠けを完全防止） -->
            <div class="cyber-glass p-5 md:col-span-6 flex flex-col items-center justify-between">
                <div class="w-full flex justify-between items-center mb-1">
                    <h2 class="text-cyan-400 font-bold text-xs uppercase flex items-center gap-2">
                        <span class="w-2 h-2 bg-cyan-400 rounded-sm"></span>
                        Capability Matrix
                    </h2>
                    <span class="text-[10px] text-slate-400">STATUS LEVEL</span>
                </div>

                <div class="w-full aspect-square max-w-[320px] flex items-center justify-center p-2">
                    <canvas id="radarChart"></canvas>
                </div>

                <div class="w-full border-t border-slate-800/80 pt-2.5 flex justify-between items-center text-[11px] text-slate-400">
                    <span>OVERALL RATING</span>
                    <span class="font-bold text-cyan-300 text-sm">91.0 <span class="text-[9px] text-slate-500 font-normal">/ 100 PTS</span></span>
                </div>
            </div>

            <!-- 右側：テレメトリ ＆ ライブCLIログ -->
            <div class="cyber-glass p-5 md:col-span-6 flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-cyan-400 font-bold text-xs uppercase flex items-center gap-2">
                            <span class="w-2 h-2 bg-cyan-400 rounded-sm"></span>
                            Runtime Telemetry
                        </h2>
                        <span class="text-[10px] text-emerald-400 font-bold tracking-widest flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> LIVE
                        </span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400">Compute Platform</span>
                            <span class="text-emerald-400 font-bold"><?php echo htmlspecialchars($PROFILE['environment']); ?></span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400">Traffic Routing</span>
                            <span class="text-cyan-300 font-bold"><?php echo htmlspecialchars($PROFILE['load_balancer']); ?></span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400">Database Cluster</span>
                            <span class="text-purple-300 font-bold"><?php echo htmlspecialchars($PROFILE['db_engine']); ?></span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400">Identity Provider</span>
                            <span class="text-amber-300 font-bold">GitHub OIDC (Zero-Secret)</span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-cyan-500/20 flex justify-between items-center shadow-[inset_0_0_15px_rgba(56,189,248,0.06)]">
                            <span class="text-slate-400">Session Uptime</span>
                            <span id="session-counter" class="text-cyan-400 font-bold text-sm tracking-wider">00:00:00</span>
                        </div>
                    </div>
                </div>

                <!-- ターミナル風ライブログ（SFギミック） -->
                <div>
                    <div class="text-[10px] text-slate-500 mb-1 flex items-center justify-between">
                        <span>SYSTEM EVENT MONITOR</span>
                        <span id="ping-stat" class="text-emerald-400">12ms</span>
                    </div>
                    <div id="cli-box" class="bg-slate-950/90 rounded-lg p-2.5 border border-slate-800 h-20 overflow-y-auto text-[10px] text-slate-400 space-y-1 custom-scrollbar">
                        <div><span class="text-cyan-400">[INIT]</span> Telemetry hub online.</div>
                        <div><span class="text-emerald-400">[OK]</span> ALB TargetGroup healthy: 200 OK.</div>
                        <div><span class="text-indigo-400">[AUTH]</span> OIDC Token verified with AWS STS.</div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- 演出スクリプト群 -->
    <script>
        // 1. 動的背景パーティクルネットワーク（浮遊するデジタルノード）
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        for (let i = 0; i < 45; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4,
                r: Math.random() * 1.5 + 1
            });
        }

        function drawParticles() {
            ctx.clearRect(0, 0, width, height);
            ctx.fillStyle = 'rgba(56, 189, 248, 0.4)';
            ctx.strokeStyle = 'rgba(56, 189, 248, 0.08)';

            for (let i = 0; i < particles.length; i++) {
                let p = particles[i];
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    let p2 = particles[j];
                    let dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                    if (dist < 130) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(drawParticles);
        }
        drawParticles();

        // 2. レーダーチャート（フォント縮小＆レイアウト最適化で欠けをゼロに）
        const rCtx = document.getElementById('radarChart').getContext('2d');
        new Chart(rCtx, {
            type: 'radar',
            data: {
                labels: <?php echo $chart_labels; ?>,
                datasets: [{
                    data: <?php echo $chart_values; ?>,
                    backgroundColor: 'rgba(56, 189, 248, 0.22)',
                    borderColor: '#38bdf8',
                    borderWidth: 2,
                    pointBackgroundColor: '#38bdf8',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                layout: {
                    padding: { top: 10, bottom: 10, left: 10, right: 10 }
                },
                animation: {
                    duration: 1800,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        angleLines: { color: 'rgba(56, 189, 248, 0.2)' },
                        grid: { color: 'rgba(56, 189, 248, 0.12)' },
                        pointLabels: {
                            color: '#94a3b8',
                            font: { size: 10, weight: '700', family: 'monospace' }
                        },
                        ticks: { display: false, stepSize: 20 },
                        min: 0,
                        max: 100
                    }
                }
            }
        });

        // 3. タイマー & 疑似ログストリーミング
        let sec = 0;
        const timer = document.getElementById('session-counter');
        const cli = document.getElementById('cli-box');
        const ping = document.getElementById('ping-stat');

        const sampleLogs = [
            '<span class="text-cyan-400">[INFO]</span> Container memory usage: 142MB / 512MB.',
            '<span class="text-emerald-400">[OK]</span> RDS MySQL connection pool stable.',
            '<span class="text-indigo-400">[NET]</span> TLS handshake verified via ALB.',
            '<span class="text-amber-400">[HEARTBEAT]</span> Health check ping received.'
        ];

        setInterval(() => {
            sec++;
            const h = String(Math.floor(sec / 3600)).padStart(2, '0');
            const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            timer.textContent = `${h}:${m}:${s}`;

            // 5秒ごとにログが1行流れる
            if (sec % 5 === 0) {
                const log = sampleLogs[Math.floor(Math.random() * sampleLogs.length)];
                const line = document.createElement('div');
                line.innerHTML = log;
                cli.appendChild(line);
                cli.scrollTop = cli.scrollHeight;
                ping.textContent = `${Math.floor(Math.random() * 8 + 10)}ms`;
            }
        }, 1000);
    </script>
</body>
</html>