<style>
	/* Halaman auth (login, setup, lupa & reset kata sandi) — kartu di tengah,
	   mengikuti design system app (Inter, kartu putih membulat, Bootstrap). */
	.auth-shell {
		min-height: calc(100vh - 130px);
		display: flex;
		align-items: flex-start;
		justify-content: center;
		padding: 48px 16px 32px;
	}
	.auth-card {
		width: 100%;
		max-width: 420px;
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 14px;
		box-shadow: 0 10px 30px rgba(17, 24, 39, .06);
		padding: 32px 28px;
	}
	.auth-brand {
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 10px;
		font-weight: 700;
		font-size: 1.2rem;
		color: #111827;
		text-decoration: none;
	}
	.auth-brand:hover {
		color: #111827;
		text-decoration: none;
	}
	.auth-brand img {
		height: 32px;
		width: auto;
	}
	.auth-card__subtitle {
		text-align: center;
		color: #6b7280;
		font-size: .9rem;
		margin-top: 4px;
		margin-bottom: 22px;
	}
	.auth-alert {
		border-radius: 10px;
		padding: 10px 14px;
		font-size: .875rem;
		line-height: 1.45;
		margin-bottom: 18px;
	}
	.auth-alert--error {
		background: #fde8e8;
		color: #b42318;
		border: 1px solid #f4c7c7;
	}
	.auth-alert--success {
		background: #e7f6ec;
		color: #1e7e34;
		border: 1px solid #bfe6cb;
	}
	.auth-card__footer {
		text-align: center;
		margin-top: 18px;
		font-size: .875rem;
		color: #6b7280;
	}
</style>
