const express = require('express');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// ─── In-Memory Database ────────────────────────────────────────────────────
let reports = [
  {
    id: 'R001',
    citizenName: 'Kamal Perera',
    phone: '077-123-4567',
    location: 'Kurunegala Town, Main Street',
    type: 'Illegal Dumping',
    description: 'Large pile of garbage dumped near the bus stand.',
    status: 'pending',
    priority: 'high',
    timestamp: new Date(Date.now() - 86400000 * 2).toISOString(),
    image: null,
    notes: []
  },
  {
    id: 'R002',
    citizenName: 'Nimal Silva',
    phone: '071-987-6543',
    location: 'Wayamba Road, Near School',
    type: 'Overflowing Bin',
    description: 'Public bin has not been collected for over a week.',
    status: 'in-progress',
    priority: 'medium',
    timestamp: new Date(Date.now() - 86400000).toISOString(),
    image: null,
    notes: ['Assigned to Team B']
  },
  {
    id: 'R003',
    citizenName: 'Saman Wickrama',
    phone: '076-555-1234',
    location: 'Dambulla Road Junction',
    type: 'Hazardous Waste',
    description: 'Chemical drums abandoned on roadside.',
    status: 'resolved',
    priority: 'high',
    timestamp: new Date(Date.now() - 86400000 * 5).toISOString(),
    image: null,
    notes: ['Collected by hazmat team', 'Resolved on schedule']
  },
  {
    id: 'R004',
    citizenName: 'Chamari Fernando',
    phone: '078-444-9876',
    location: 'Kurunegala Lake Area',
    type: 'Burning Waste',
    description: 'Someone is burning plastic waste near the lake causing smoke.',
    status: 'pending',
    priority: 'urgent',
    timestamp: new Date(Date.now() - 3600000).toISOString(),
    image: null,
    notes: []
  },
  {
    id: 'R005',
    citizenName: 'Roshan Mendis',
    phone: '070-333-2211',
    location: 'Puttalam Road, Km 3',
    type: 'Missed Collection',
    description: 'Garbage truck has not visited our street this week.',
    status: 'in-progress',
    priority: 'low',
    timestamp: new Date(Date.now() - 86400000 * 3).toISOString(),
    image: null,
    notes: ['Notified collection team']
  }
];

let idCounter = 6;

// Helper: generate ID
function generateId() {
  return 'R' + String(idCounter++).padStart(3, '0');
}

// ─── STATS ─────────────────────────────────────────────────────────────────
app.get('/api/stats', (req, res) => {
  const total = reports.length;
  const pending = reports.filter(r => r.status === 'pending').length;
  const inProgress = reports.filter(r => r.status === 'in-progress').length;
  const resolved = reports.filter(r => r.status === 'resolved').length;
  const urgent = reports.filter(r => r.priority === 'urgent').length;

  const typeCount = {};
  reports.forEach(r => {
    typeCount[r.type] = (typeCount[r.type] || 0) + 1;
  });

  const recentReports = [...reports]
    .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
    .slice(0, 5);

  res.json({ total, pending, inProgress, resolved, urgent, typeCount, recentReports });
});

// ─── REPORTS CRUD ───────────────────────────────────────────────────────────

// GET all reports (with optional filters)
app.get('/api/reports', (req, res) => {
  let result = [...reports];
  const { status, priority, type, search } = req.query;

  if (status && status !== 'all') result = result.filter(r => r.status === status);
  if (priority && priority !== 'all') result = result.filter(r => r.priority === priority);
  if (type && type !== 'all') result = result.filter(r => r.type === type);
  if (search) {
    const q = search.toLowerCase();
    result = result.filter(r =>
      r.citizenName.toLowerCase().includes(q) ||
      r.location.toLowerCase().includes(q) ||
      r.description.toLowerCase().includes(q) ||
      r.id.toLowerCase().includes(q)
    );
  }

  result.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
  res.json(result);
});

// GET single report
app.get('/api/reports/:id', (req, res) => {
  const report = reports.find(r => r.id === req.params.id);
  if (!report) return res.status(404).json({ error: 'Report not found' });
  res.json(report);
});

// POST create report (Citizen App)
app.post('/api/reports', (req, res) => {
  const { citizenName, phone, location, type, description, image } = req.body;

  if (!citizenName || !location || !type || !description) {
    return res.status(400).json({ error: 'Missing required fields: citizenName, location, type, description' });
  }

  const newReport = {
    id: generateId(),
    citizenName,
    phone: phone || 'Not provided',
    location,
    type,
    description,
    status: 'pending',
    priority: 'medium',
    timestamp: new Date().toISOString(),
    image: image || null,
    notes: []
  };

  reports.push(newReport);
  res.status(201).json({ success: true, report: newReport });
});

// PATCH update report status/priority (Admin)
app.patch('/api/reports/:id', (req, res) => {
  const report = reports.find(r => r.id === req.params.id);
  if (!report) return res.status(404).json({ error: 'Report not found' });

  const { status, priority, note } = req.body;
  if (status) report.status = status;
  if (priority) report.priority = priority;
  if (note) report.notes.push(note);

  res.json({ success: true, report });
});

// DELETE report (Admin)
app.delete('/api/reports/:id', (req, res) => {
  const index = reports.findIndex(r => r.id === req.params.id);
  if (index === -1) return res.status(404).json({ error: 'Report not found' });
  reports.splice(index, 1);
  res.json({ success: true });
});

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok', message: 'Garbage Reporting API running', timestamp: new Date().toISOString() });
});

app.listen(PORT, () => {
  console.log(`\n🗑️  Garbage Reporting API running at http://localhost:${PORT}`);
  console.log(`📊 Admin Dashboard: open admin-dashboard/index.html`);
  console.log(`📱 Citizen App:     open citizen-app/index.html\n`);
});
