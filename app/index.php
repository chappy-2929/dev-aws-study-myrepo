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
    'badge_role'   => 'All AWS Certifications Engineer',
    'status_badge' => 'OPERATIONAL 100%',
    
    // アバター画像URL
    'avatar_url'   => 'https://github.com/chappy-2929.png',
    
    // 自己紹介 / 達成コメント
    'bio'          => "Terraform × ECS Fargate × GitHub Actions OIDC 完全制覇！\nAWS勉強会の講師として日々奮闘中の26歳。\n3月に👶産まれたよ。",
    
    // ポップな実績バッジ（自由に増減可能）
    'achievements' => [
        ['icon' => 'award',   'label' => 'All AWS Certified', 'color' => 'amber'],
        ['icon' => 'server',  'label' => 'ECS Architect',      'color' => 'cyan'],
        ['icon' => 'heart',   'label' => 'Baby Born (2026.03)', 'color' => 'rose'],
        ['icon' => 'sparkles','label' => 'Hands-on Master',    'color' => 'emerald'],
    ],
    
    // 6角形レーダーチャートのパラメータ（各項目 0〜100）
    'parameters'   => [
        'IaC (Terraform)'   => 92,
        'AWS Architecture'  => 88,
        'Docker Container'  => 82,
        'CI/CD Automation'  => 96,
        'IAM & Security'    => 90,
        'Troubleshooting'   => 100, // 泥臭い切り分け力！
    ],
    
    // テレメトリスペック
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
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><polygon points='50 3, 90 25, 90 75, 50 97, 10 75, 10 25' fill='%23050b14' stroke='%2338bdf8' stroke-width='6'/><polygon points='50 20, 75 35, 75 65, 50 80, 25 65, 25 35' fill='%2338bdf8'/></svg>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Google Fonts: Orbitron & Rajdhani -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #030712;
            color: #f1f5f9;
            font-family: 'Rajdhani', sans-serif;
            overflow-x: hidden;
        }

        .font-orbitron {
            font-family: 'Orbitron', monospace;
        }

        #bg-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        .cyber-grid {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(56, 189, 248, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(56, 189, 248, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 1;
        }

        .cyber-glass {
            background: rgba(11, 19, 38, 0.82);
            border: 1px solid rgba(56, 189, 248, 0.3);
            backdrop-filter: blur(24px);
            border-radius: 1.25rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.75), 0 0 25px rgba(56, 189, 248, 0.12);
            position: relative;
            overflow: hidden;
        }

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

        .text-neon-cyan {
            text-shadow: 0 0 15px rgba(56, 189, 248, 0.6), 0 0 30px rgba(56, 189, 248, 0.2);
        }

        @keyframes pulse-dot {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.2); opacity: 1; filter: drop-shadow(0 0 8px #10b981); }
            100% { transform: scale(0.9); opacity: 0.7; }
        }
        .pulse-live {
            animation: pulse-dot 2s infinite ease-in-out;
        }

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
                
                <!-- アバター -->
                <div class="relative group">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border-2 border-cyan-400 p-1 bg-slate-950 shadow-[0_0_25px_rgba(56,189,248,0.4)] transition-transform duration-300 group-hover:scale-105">
                        <img src="<?php echo htmlspecialchars($PROFILE['avatar_url']); ?>" 
                             alt="Avatar" 
                             class="w-full h-full object-cover rounded-xl"
                             onerror="this.src='https://api.dicebear.com/7.x/bottts/svg?seed=fallback'">
                    </div>
                    <span class="absolute -bottom-2 -right-2 flex items-center gap-1 bg-slate-950 border border-emerald-500/80 px-2 py-0.5 rounded-full shadow-lg">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-live"></span>
                        <span class="text-[10px] font-bold text-emerald-400 font-orbitron">ONLINE</span>
                    </span>
                </div>

                <!-- ユーザー情報 -->
                <div class="flex-1 text-center sm:text-left space-y-3">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="px-3 py-0.5 rounded-md text-xs font-bold font-orbitron bg-emerald-950/90 text-emerald-400 border border-emerald-500/50 flex items-center gap-1.5 shadow-[0_0_12px_rgba(16,185,129,0.25)]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-live"></span>
                            <?php echo htmlspecialchars($PROFILE['status_badge']); ?>
                        </span>
                        <span class="px-3 py-0.5 rounded-md text-xs font-bold font-orbitron bg-cyan-950/90 text-cyan-300 border border-cyan-500/50 shadow-[0_0_12px_rgba(56,189,248,0.25)]">
                            <?php echo htmlspecialchars($PROFILE['badge_role']); ?>
                        </span>
                    </div>

                    <!-- プレイヤー名（Orbitron 特大文字 & ネオングロー） -->
                    <h1 class="text-4xl sm:text-5xl font-black tracking-wider text-white font-orbitron flex items-center justify-center sm:justify-start gap-3">
                        <span class="text-neon-cyan"><?php echo htmlspecialchars($PROFILE['player_name']); ?></span>
                        <span class="text-[11px] tracking-widest text-cyan-300 border border-cyan-400/50 px-2 py-0.5 rounded bg-cyan-950/50 shadow">NODE #01</span>
                    </h1>

                    <!-- 実績・ポップタグバッジ一覧 -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                        <?php foreach ($PROFILE['achievements'] as $ach): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-900/90 border border-slate-700/80 shadow-sm">
                                <i data-lucide="<?php echo htmlspecialchars($ach['icon']); ?>" class="w-3.5 h-3.5 text-<?php echo $ach['color']; ?>-400"></i>
                                <span class="text-slate-200"><?php echo htmlspecialchars($ach['label']); ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed whitespace-pre-line font-sans pt-1">
<?php echo htmlspecialchars($PROFILE['bio']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- コアダッシュボード -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
            
            <!-- レーダーチャート -->
            <div class="cyber-glass p-5 md:col-span-6 flex flex-col items-center justify-between">
                <div class="w-full flex justify-between items-center mb-1">
                    <h2 class="text-cyan-400 font-bold text-sm uppercase font-orbitron flex items-center gap-2">
                        <i data-lucide="crosshair" class="w-4 h-4 text-cyan-400"></i>
                        Capability Matrix
                    </h2>
                    <span class="text-xs text-slate-400 font-orbitron">HEXAGON</span>
                </div>

                <div class="w-full aspect-square max-w-[320px] flex items-center justify-center p-2">
                    <canvas id="radarChart"></canvas>
                </div>

                <div class="w-full border-t border-slate-800/80 pt-2.5 flex justify-between items-center text-xs text-slate-400 font-orbitron">
                    <span>OVERALL RATING</span>
                    <span class="font-black text-cyan-300 text-lg">91.0 <span class="text-[10px] text-slate-500 font-normal">/ 100 PTS</span></span>
                </div>
            </div>

            <!-- テレメトリ ＆ ライブCLIログ -->
            <div class="cyber-glass p-5 md:col-span-6 flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-cyan-400 font-bold text-sm uppercase font-orbitron flex items-center gap-2">
                            <i data-lucide="activity" class="w-4 h-4 text-cyan-400"></i>
                            Live Telemetry
                        </h2>
                        <span class="text-xs text-emerald-400 font-bold font-orbitron tracking-widest flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-live"></span> STREAMING
                        </span>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i data-lucide="cpu" class="w-4 h-4 text-cyan-400"></i> Platform
                            </span>
                            <span class="text-emerald-400 font-bold font-mono text-xs"><?php echo htmlspecialchars($PROFILE['environment']); ?></span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i data-lucide="git-pull-request" class="w-4 h-4 text-cyan-400"></i> Traffic
                            </span>
                            <span class="text-cyan-300 font-bold font-mono text-xs"><?php echo htmlspecialchars($PROFILE['load_balancer']); ?></span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i data-lucide="database" class="w-4 h-4 text-cyan-400"></i> Database
                            </span>
                            <span class="text-purple-300 font-bold font-mono text-xs"><?php echo htmlspecialchars($PROFILE['db_engine']); ?></span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-slate-800/80 flex justify-between items-center">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i data-lucide="shield-check" class="w-4 h-4 text-cyan-400"></i> Auth
                            </span>
                            <span class="text-amber-300 font-bold font-mono text-xs">GitHub OIDC (Zero-Secret)</span>
                        </div>
                        <div class="bg-slate-950/70 p-2.5 rounded-lg border border-cyan-500/30 flex justify-between items-center shadow-[inset_0_0_15px_rgba(56,189,248,0.08)]">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-cyan-400"></i> Session Uptime
                            </span>
                            <span id="session-counter" class="text-cyan-300 font-black text-base font-orbitron tracking-widest text-neon-cyan">00:00:00</span>
                        </div>
                    </div>
                </div>

                <!-- ターミナル風ライブログ -->
                <div>
                    <div class="text-[11px] text-slate-500 mb-1 flex items-center justify-between font-mono">
                        <span class="flex items-center gap-1.5"><i data-lucide="terminal" class="w-3.5 h-3.5 text-slate-400"></i> EVENT LOG</span>
                        <span id="ping-stat" class="text-emerald-400 font-bold">12ms</span>
                    </div>
                    <div id="cli-box" class="bg-slate-950/90 rounded-lg p-2.5 border border-slate-800 h-20 overflow-y-auto text-xs text-slate-400 space-y-1 font-mono custom-scrollbar">
                        <div><span class="text-cyan-400">[INIT]</span> Telemetry hub initialized.</div>
                        <div><span class="text-emerald-400">[OK]</span> ALB TargetGroup healthy: 200 OK.</div>
                        <div><span class="text-indigo-400">[AUTH]</span> OIDC Token verified with AWS STS.</div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script>
        // Lucide アイコン初期化
        lucide.createIcons();

        // 1. 高速・高密度宇宙パーティクル
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];
        let mouse = { x: null, y: null, radius: 130 };

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.x;
            mouse.y = e.y;
        });
        window.addEventListener('mouseout', () => {
            mouse.x = null;
            mouse.y = null;
        });
        resize();

        for (let i = 0; i < 85; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 1.4,
                vy: (Math.random() - 0.5) * 1.4,
                r: Math.random() * 2 + 0.8
            });
        }

        function drawParticles() {
            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < particles.length; i++) {
                let p = particles[i];
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;

                if (mouse.x !== null) {
                    let dx = mouse.x - p.x;
                    let dy = mouse.y - p.y;
                    let distance = Math.hypot(dx, dy);
                    if (distance < mouse.radius) {
                        let force = (mouse.radius - distance) / mouse.radius;
                        p.x -= (dx / distance) * force * 3.5;
                        p.y -= (dy / distance) * force * 3.5;
                    }
                }

                ctx.fillStyle = 'rgba(56, 189, 248, 0.65)';
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    let p2 = particles[j];
                    let dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                    if (dist < 115) {
                        let opacity = (1 - dist / 115) * 0.28;
                        ctx.strokeStyle = `rgba(56, 189, 248, ${opacity})`;
                        ctx.lineWidth = 1;
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

        // 2. レーダーチャート
        const rCtx = document.getElementById('radarChart').getContext('2d');
        new Chart(rCtx, {
            type: 'radar',
            data: {
                labels: <?php echo $chart_labels; ?>,
                datasets: [{
                    data: <?php echo $chart_values; ?>,
                    backgroundColor: 'rgba(56, 189, 248, 0.25)',
                    borderColor: '#38bdf8',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#38bdf8',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 4.5,
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
                            font: { size: 11, weight: '700', family: 'Rajdhani' }
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
            '<span class="text-amber-400">[HEARTBEAT]</span> Health check ping received.',
            '<span class="text-purple-400">[STORAGE]</span> EFS volume mount latency: 1.2ms.'
        ];

        setInterval(() => {
            sec++;
            const h = String(Math.floor(sec / 3600)).padStart(2, '0');
            const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            timer.textContent = `${h}:${m}:${s}`;

            if (sec % 4 === 0) {
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