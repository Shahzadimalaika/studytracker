// ======================= 🔃 Load Study Log Form =======================
function loadStudyForm() {
  fetch('add_log_form.html')
    .then(res => res.text())
    .then(html => {
      document.getElementById('logFormArea').innerHTML = html;
    })
    .catch(() => {
      document.getElementById('logFormArea').innerHTML =
        '<p class="text-red-600">Failed to load form. Try again later.</p>';
    });
}

// ======================= ✅ Submit Study Log =======================
function submitStudyLog(e) {
  e.preventDefault();
  const subject = document.getElementById('subject').value.trim();
  const duration = parseInt(document.getElementById('duration').value);
  const study_date = document.getElementById('study_date').value;
  if (!subject || isNaN(duration) || duration <= 0 || !study_date) {
    alert("Please fill in all fields correctly.");
    return;
  }
  fetch('api/study/add_log.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ subject, duration, study_date })
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.success) {
        document.getElementById('studyLogForm').reset();
        renderAllData();
      }
    })
    .catch(() => alert("Failed to submit log. Please try again."));
}

// ======================= 📊 Subject Pie Chart =======================
function renderSubjectPieChart() {
  fetch('api/study/get_subject_summary.php')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('subjectPieChart').parentNode;
      if (data.success && data.subjects.length > 0) {
        const ctx = document.getElementById('subjectPieChart').getContext('2d');
        if (window.subjectChart) window.subjectChart.destroy();
        window.subjectChart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: data.subjects,
            datasets: [{
              data: data.minutes,
              backgroundColor: ['#60A5FA', '#FCD34D', '#F87171', '#34D399', '#A78BFA', '#F472B6']
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
          }
        });
      } else {
        if (!container.querySelector('.no-data')) {
          const msg = document.createElement('p');
          msg.className = 'no-data text-gray-500 text-sm text-center mt-4';
          msg.textContent = 'No subject data yet. Log some study time!';
          container.appendChild(msg);
        }
      }
    })
    .catch(() => console.error("Failed to load subject data."));
}

// ======================= 📅 Weekly Progress =======================
function renderWeeklyProgressChart() {
  fetch('api/study/get_weekly_progress.php')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('weeklyBarChart').parentNode;
      if (data.success && data.dates.length > 0) {
        const ctx = document.getElementById('weeklyBarChart').getContext('2d');
        if (window.weeklyChart) window.weeklyChart.destroy();
        window.weeklyChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: data.dates,
            datasets: [{
              label: 'Minutes Studied',
              data: data.minutes,
              backgroundColor: '#3B82F6'
            }]
          },
          options: {
            scales: { y: { beginAtZero: true } },
            responsive: true,
            plugins: { legend: { display: false } }
          }
        });
      } else {
        if (!container.querySelector('.no-data')) {
          const msg = document.createElement('p');
          msg.className = 'no-data text-gray-500 text-sm text-center mt-4';
          msg.textContent = 'No weekly data yet. Add your logs!';
          container.appendChild(msg);
        }
      }
    })
    .catch(() => console.error("Failed to load weekly progress data."));
}

// ======================= 🎯 Daily Goal Progress Ring =======================
function renderDailyGoalProgress() {
  fetch('api/user/profile.php')
    .then(res => res.json())
    .then(userRes => {
      if (!userRes.success) return;
      const goal = userRes.user.daily_goal || 0;
      document.getElementById('currentGoal').textContent = goal;
      fetch('api/study/get_today_total.php')
        .then(res => res.json())
        .then(logRes => {
          const total = logRes.total || 0;
          const percent = Math.min((total / goal) * 100, 100);
          const ctx = document.getElementById('dailyGoalRing').getContext('2d');
          if (window.dailyGoalChart) window.dailyGoalChart.destroy();
          window.dailyGoalChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: ['Completed', 'Remaining'],
              datasets: [{
                data: [total, Math.max(goal - total, 0)],
                backgroundColor: ['#34D399', '#E5E7EB'],
                borderWidth: 0
              }]
            },
            options: {
              cutout: '75%',
              plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
              }
            },
            plugins: [{
              id: 'centerText',
              beforeDraw(chart) {
                const { width, height, ctx } = chart;
                ctx.restore();
                ctx.font = `${(height / 5).toFixed(0)}px sans-serif`;
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#111827';
                const text = `${Math.round(percent)}%`;
                const textX = (width - ctx.measureText(text).width) / 2;
                const textY = height / 2;
                ctx.fillText(text, textX, textY);
                ctx.save();
              }
            }]
          });
        });
    });
}

// ======================= 🔥 Streak Tracker =======================
function renderStreak() {
  fetch('api/study/get_streak.php')
    .then(res => res.json())
    .then(data => {
      const el = document.getElementById('streakDisplay');
      el.textContent = data.success ? (data.streak > 0 ? `🔥 ${data.streak}-Day Streak!` : 'No active streak yet') : 'Error loading streak';
    })
    .catch(() => {
      document.getElementById('streakDisplay').textContent = 'Error loading streak';
    });
}

// ======================= 🏆 Top Subjects =======================
function renderTopSubjects() {
  fetch('api/study/get_top_subject.php')
    .then(res => res.json())
    .then(data => {
      if (!data.success) return;
      const list = document.getElementById('topSubjectsList');
      list.innerHTML = '';
      data.subjects.forEach(sub => {
        const li = document.createElement('li');
        li.className = 'flex justify-between text-gray-700';
        li.innerHTML = `<span>${sub.subject}</span><span>${sub.total_minutes} min (${sub.percentage}%)</span>`;
        list.appendChild(li);
      });
    });
}

// ======================= 📆 Calendar =======================
let calendarState = { year: new Date().getFullYear(), month: new Date().getMonth() };
function changeMonth(offset) {
  calendarState.month += offset;
  if (calendarState.month > 11) { calendarState.month = 0; calendarState.year++; }
  else if (calendarState.month < 0) { calendarState.month = 11; calendarState.year--; }
  renderStudyCalendar();
}
function renderStudyCalendar() {
  const calendar = document.getElementById('calendar');
  const calendarDays = document.getElementById('calendarDays');
  const monthLabel = document.getElementById('calendarMonth');
  if (!calendar || !monthLabel || !calendarDays) return;
  const { year, month } = calendarState;
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  monthLabel.textContent = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long' }).format(new Date(year, month));
  const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  calendarDays.innerHTML = dayNames.map(day => `<div>${day}</div>`).join('');
  fetch('api/user/profile.php')
    .then(res => res.json())
    .then(userData => {
      const goal = userData?.user?.daily_goal || 0;
      fetch(`api/study/get_calendar_data.php?year=${year}&month=${month + 1}`)
        .then(res => res.json())
        .then(data => {
          calendar.innerHTML = '';
          for (let i = 0; i < firstDay; i++) calendar.innerHTML += `<div></div>`;
          for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const minutes = data.logs[dateStr] || 0;
            let bg = 'bg-gray-100';
            if (goal > 0) {
              const percent = (minutes / goal) * 100;
              if (percent >= 100) bg = 'bg-green-300 hover:bg-green-400';
              else if (percent >= 50) bg = 'bg-yellow-300 hover:bg-yellow-400';
              else if (percent > 0) bg = 'bg-red-300 hover:bg-red-400';
            }
            calendar.innerHTML += `<div class="${bg} rounded p-2 text-center text-sm" title="${minutes} min"><div>${day}</div>${minutes > 0 ? `<div class="text-xs text-gray-700">${minutes}m</div>` : ''}</div>`;
          }
        });
    });
}

// ======================= 🍅 Pomodoro Timer =======================
let pomodoroTime = 0;
let pomodoroInterval = null;
let sessionDuration = 0;
function startPomodoro(minutes) {
  const subject = document.getElementById('pomodoroSubject').value;
  if (minutes === 25 && !subject) {
    alert("Please select a subject for this session.");
    return;
  }
  sessionDuration = minutes;
  pomodoroTime = minutes * 60;
  document.getElementById('pomodoroDisplay').textContent = formatTime(pomodoroTime);
  document.getElementById('pomodoroLogArea').classList.add('hidden');
  clearInterval(pomodoroInterval);
  pomodoroInterval = setInterval(() => {
    pomodoroTime--;
    document.getElementById('pomodoroDisplay').textContent = formatTime(pomodoroTime);
    if (pomodoroTime <= 0) {
      clearInterval(pomodoroInterval);
      pomodoroInterval = null;
      document.getElementById('pomodoroDisplay').textContent = '✔️ Done!';
      if (sessionDuration >= 25) {
        document.getElementById('pomodoroLogArea').classList.remove('hidden');
      }
    }
  }, 1000);
}
function resetPomodoro() {
  clearInterval(pomodoroInterval);
  pomodoroInterval = null;
  sessionDuration = 0;
  pomodoroTime = 0;
  document.getElementById('pomodoroDisplay').textContent = '25:00';
  document.getElementById('pomodoroLogArea').classList.add('hidden');
}
function formatTime(seconds) {
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
}
function logPomodoroSession() {
  const subject = document.getElementById('pomodoroSubject').value || 'Pomodoro Session';
  const today = new Date().toISOString().split('T')[0];
  const payload = { subject, duration: sessionDuration, study_date: today };
  fetch('api/study/add_log.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message || 'Session logged!');
      if (data.success) {
        resetPomodoro();
        renderAllData();
      }
    });
}

// ======================= 🚀 Init All Charts =======================
function renderAllData() {
  renderSubjectPieChart();
  renderWeeklyProgressChart();
  renderDailyGoalProgress();
  renderTopSubjects();
  renderStreak();
  renderStudyCalendar();
}

// ======================= 🔒 Logout =======================
function logout() {
  fetch('api/auth/logout.php')
    .then(() => { window.location.href = 'login.html'; })
    .catch(() => alert("Logout failed. Try again."));
}

// ======================= 📦 On DOM Load =======================
document.addEventListener('DOMContentLoaded', () => {
  const goalForm = document.getElementById('goalForm');
  if (goalForm) {
    goalForm.addEventListener('submit', e => {
      e.preventDefault();
      const goal = parseInt(document.getElementById('dailyGoalInput').value);
      if (!goal || goal < 10) {
        alert("Please enter a valid goal (min 10 minutes).");
        return;
      }
      fetch('api/user/update_goal.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ daily_goal: goal })
      })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          renderDailyGoalProgress();
          renderStudyCalendar();
        });
    });
  }
  renderAllData();
});
async function exportDashboardPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  doc.setFontSize(16);
  doc.text("📊 Study Tracker Summary", 20, 20);

  // Example content — you can expand this with real-time values
  const username = document.getElementById("welcomeText").textContent || "User";
  const goal = document.getElementById("currentGoal").textContent || "--";
  const streakText = document.getElementById("streakDisplay")?.textContent || "--";

  doc.setFontSize(12);
  doc.text(`User: ${username}`, 20, 35);
  doc.text(`Daily Goal: ${goal} minutes`, 20, 45);
  doc.text(`Streak: ${streakText}`, 20, 55);

  // Optionally add charts as images:
  const subjectChart = document.getElementById("subjectPieChart");
  const chartImage = subjectChart.toDataURL("image/png", 1.0);
  doc.addImage(chartImage, 'PNG', 20, 70, 160, 90); // x, y, width, height

  doc.save("study-summary.pdf");
}
