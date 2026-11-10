body {
    margin: 0;
    background: #f3f7fb;
    font-family: Arial, sans-serif;
    color: #1f2937;
}

* {
    box-sizing: border-box;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 18px 48px;
}

.header {
    margin-bottom: 24px;
}

.eyebrow {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #4f46e5;
    margin-bottom: 8px;
}

h1 {
    margin: 0;
    font-size: 2.5rem;
}

h2 {
    margin-top: 0;
    margin-bottom: 18px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(31, 41, 55, 0.06);
    border-left: 6px solid #2563eb;
}

.card.warning {
    border-left-color: #f59e0b;
}

.card.success {
    border-left-color: #10b981;
}

.card span {
    display: block;
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 10px;
}

.card strong {
    font-size: 1.8rem;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.panel {
    background: #fff;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 10px 25px rgba(31, 41, 55, 0.06);
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
    padding: 10px 12px;
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
    background: #2563eb;
    color: white;
    border: none;
    cursor: pointer;
    font-weight: 700;
}

button:hover {
    background: #1d4ed8;
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
    background: #f9fafb;
}

@media (max-width: 700px) {
    h1 {
        font-size: 2rem;
    }
}
