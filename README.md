body {
    margin: 0;
    background: linear-gradient(135deg, #eef4ff 0%, #f7fafc 100%);
    font-family: Arial, sans-serif;
    color: #1f2937;
}

* {
    box-sizing: border-box;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 32px 20px 48px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}

.eyebrow {
    margin: 0 0 8px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #2563eb;
    font-weight: 700;
}

h1 {
    margin: 0;
    font-size: 2.7rem;
}

h2 {
    margin-top: 0;
    margin-bottom: 18px;
}

.topbar-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.nav-button {
    display: inline-block;
    background: #2563eb;
    color: white;
    text-decoration: none;
    padding: 11px 18px;
    border-radius: 12px;
    font-weight: 700;
}

.nav-button.secondary {
    background: #e5e7eb;
    color: #1f2937;
}

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}

.card {
    background: #fff;
    padding: 22px;
    border-radius: 16px;
    box-shadow: 0 12px 25px rgba(31, 41, 55, 0.06);
    border-left: 6px solid #2563eb;
}

.card.warning {
    border-left-color: #f59e0b;
}

.card.success {
    border-left-color: #10b981;
}

.card.income {
    border-left-color: #8b5cf6;
}

.card span {
    display: block;
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 10px;
}

.card strong {
    font-size: 1.7rem;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 22px;
}

.panel {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 12px 25px rgba(31, 41, 55, 0.06);
    margin-bottom: 24px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}

label {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-weight: 600;
    color: #374151;
}

input, select, button {
    padding: 11px 12px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    font-size: 1rem;
}

input:focus, select:focus {
    outline: 2px solid rgba(37, 99, 235, 0.2);
    border-color: #2563eb;
}

button {
    align-self: end;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    border: none;
    cursor: pointer;
    font-weight: 700;
}

button:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
}

.alerts {
    margin-bottom: 22px;
}

.alert {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 10px;
}

.alert-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 10px;
}

.alert-list li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 10px;
    padding: 12px 14px;
    color: #9a4d00;
    flex-wrap: wrap;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 10px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

th {
    background: #f8fafc;
}

.auth-body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
}

.auth-card {
    width: min(420px, 92vw);
    background: #ffffff;
    border-radius: 18px;
    padding: 30px 24px;
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.12);
}

.auth-subtitle,
.auth-footnote {
    color: #6b7280;
    text-align: center;
}

.auth-form {
    display: grid;
    gap: 16px;
    margin-top: 18px;
}

@media (max-width: 700px) {
    h1 {
        font-size: 2.2rem;
    }

    .topbar {
        align-items: flex-start;
    }
}
