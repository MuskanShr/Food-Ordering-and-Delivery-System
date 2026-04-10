
<style>
.profile-wrap {
    max-width: 860px;
    margin: 2.5rem auto;
    padding: 0 2rem 4rem;
}

/* ── Header card ── */
.profile-hero {
    background: linear-gradient(135deg, var(--orange), var(--orange-dark));
    border-radius: 20px;
    padding: 2.5rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 1.8rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(232,82,26,0.28);
}
.profile-hero::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 260px; height: 260px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
}
.profile-hero::after {
    content: '';
    position: absolute;
    right: 60px; bottom: -80px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}
.avatar {
    width: 88px; height: 88px;
    border-radius: 50%;
    background: rgba(255,255,255,0.22);
    border: 3px solid rgba(255,255,255,0.5);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.4rem;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
}
.profile-meta { color: white; z-index: 1; }
.profile-meta h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem; font-weight: 800;
    margin-bottom: 0.3rem;
}
.profile-meta .email {
    opacity: 0.85; font-size: 0.9rem; margin-bottom: 0.6rem;
}
.role-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.35);
    border-radius: 20px;
    padding: 0.25rem 0.85rem;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    backdrop-filter: blur(4px);
    color: white;
}

/* ── Stats row ── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.8rem;
}
.stat-card {
    background: var(--warm-white);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.4rem 1.2rem;
    text-align: center;
    box-shadow: var(--shadow);
}
.stat-icon { font-size: 1.8rem; margin-bottom: 0.5rem; }
.stat-value {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem; font-weight: 800;
    color: var(--orange);
    line-height: 1;
    margin-bottom: 0.3rem;
}
.stat-label { font-size: 0.78rem; color: var(--gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

/* ── Info + Orders grid ── */
.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1.3fr;
    gap: 1.4rem;
}

.info-card, .orders-card {
    background: var(--warm-white);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
}
.card-header {
    padding: 1.1rem 1.4rem;
    border-bottom: 1px solid var(--border);
    font-family: 'Playfair Display', serif;
    font-size: 1.05rem; font-weight: 800;
    background: var(--cream);
    display: flex; align-items: center; gap: 0.5rem;
}
.info-list { padding: 0.6rem 0; }
.info-row {
    display: flex;
    align-items: center;
    padding: 0.85rem 1.4rem;
    border-bottom: 1px solid var(--light-gray);
    gap: 0.75rem;
}
.info-row:last-child { border-bottom: none; }
.info-icon { font-size: 1.1rem; width: 24px; flex-shrink: 0; }
.info-content {}
.info-label { font-size: 0.73rem; color: var(--gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.info-value { font-size: 0.92rem; font-weight: 600; color: var(--charcoal); margin-top: 1px; }

/* ── Recent orders ── */
.orders-list { padding: 0.6rem 0; }
.order-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.85rem 1.4rem;
    border-bottom: 1px solid var(--light-gray);
    gap: 0.5rem;
}
.order-row:last-child { border-bottom: none; }
.order-id { font-size: 0.8rem; color: var(--gray); }
.order-id strong { color: var(--charcoal); font-weight: 700; }
.order-date { font-size: 0.78rem; color: var(--gray); margin-top: 2px; }
.order-amount { font-weight: 700; color: var(--orange); font-size: 0.92rem; }
.status-pill {
    font-size: 0.7rem; font-weight: 700;
    padding: 0.2rem 0.65rem;
    border-radius: 20px;
    text-transform: uppercase; letter-spacing: 0.4px;
}
.status-Pending       { background: #FFF3E0; color: #E65100; }
.status-Preparing     { background: #E3F2FD; color: #1565C0; }
.status-Out\ for\ Delivery { background: #F3E5F5; color: #6A1B9A; }
.status-Delivered     { background: #E8F5E9; color: #2E7D32; }

.empty-orders {
    text-align: center; padding: 2.5rem 1rem;
    color: var(--gray); font-size: 0.9rem;
}
.empty-orders .ei { font-size: 2.5rem; margin-bottom: 0.5rem; }

@media (max-width: 680px) {
    .profile-grid { grid-template-columns: 1fr; }
    .stats-row { grid-template-columns: repeat(3, 1fr); }
    .profile-hero { flex-direction: column; text-align: center; }
}

.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 200;
    align-items: center; justify-content: center;
}
.modal-overlay.show { display: flex; }
.modal {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    max-width: 400px; width: 90%;
    text-align: center;
    box-shadow: var(--shadow-lg);
}
.modal h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    margin-bottom: 0.8rem;
}
.modal p { color: var(--gray); margin-bottom: 1.5rem; }
.modal-actions { display: flex; gap: 0.8rem; justify-content: center; }
</style>

<div class="profile-wrap">

    <!-- Hero -->
    <div class="profile-hero">
        <div class="avatar">
            <?= strtoupper(mb_substr($user['username'], 0, 1)) ?>
        </div>
        <div class="profile-meta">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <div class="email"> <?= htmlspecialchars($user['email']) ?></div>
            <span class="role-badge"><?= ucfirst($user['role']) ?></span>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">🛍️</div>
            <div class="stat-value"><?= $stats['total_orders'] ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">Rs <?= number_format($stats['total_spent'], 0) ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-value" style="font-size:1rem; padding-top:4px">
                <?= $stats['last_order'] ? date('M d', strtotime($stats['last_order'])) : '—' ?>
            </div>
            <div class="stat-label">Last Order</div>
        </div>
    </div>

    <!-- Info + Orders -->
    <div class="profile-grid">

        <!-- Account Info -->
        <div class="info-card">
            <div class="card-header">👤 Account Details</div>
            <div class="info-list">
                <div class="info-row">
                    <div class="info-icon">🪪</div>
                    <div class="info-content">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?= htmlspecialchars($user['username']) ?></div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📧</div>
                    <div class="info-content">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">🎭</div>
                    <div class="info-content">
                        <div class="info-label">Role</div>
                        <div class="info-value"><?= ucfirst($user['role']) ?></div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">🗓️</div>
                    <div class="info-content">
                        <div class="info-label">Member Since</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($user['created_at'])) ?></div>
                    </div>
                </div>
                <div class="info-row" style="border-bottom:none; padding-top:1rem;">
    <button onclick="document.getElementById('logoutModal').classList.add('show')"
            class="btn btn-danger" style="width:100%; justify-content:center;">
        🚪 Logout
    </button>
</div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="orders-card">
            <div class="card-header">📦 Recent Orders</div>
            <div class="orders-list">
                <?php if (empty($recentOrders)): ?>
                <div class="empty-orders">
                    <div class="ei">🛒</div>
                    <p>No orders yet.<br>
                    <a href="/foodbyte/menu.php" style="color:var(--orange);font-weight:600">Browse the menu →</a></p>
                </div>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                    <div class="order-row">
                        <div>
                            <div class="order-id">Order <strong>#<?= $order['id'] ?></strong></div>
                            <div class="order-date"><?= date('M j, Y · g:i A', strtotime($order['created_at'])) ?></div>
                        </div>
                        <div style="text-align:right">
                            <div class="order-amount">Rs <?= number_format($order['total'], 0) ?></div>
                            <span class="status-pill status-<?= $order['status'] ?>"><?= $order['status'] ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

                
<div class="modal-overlay" id="logoutModal">
    <div class="modal">
        <h3>Logging out?</h3>
        <p>Do you want to logout?</p>
        <div class="modal-actions">
            <a href="/foodbyte/logout.php" class="btn btn-danger">Yes, Logout</a>
            <button class="btn btn-outline"
                    onclick="document.getElementById('logoutModal').classList.remove('show')">
                Cancel
            </button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>