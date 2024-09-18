// Seleção dos elementos
const reminderForm = document.getElementById('reminderForm');
const reminderInput = document.getElementById('reminderInput');
const reminderTime = document.getElementById('reminderTime');
const reminderList = document.getElementById('reminderList');

// Carregar lembretes salvos do localStorage
document.addEventListener('DOMContentLoaded', loadReminders);

// Evento de submit no formulário
reminderForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const reminderText = reminderInput.value;
    const reminderDate = new Date(reminderTime.value).toLocaleString();

    // Criar objeto lembrete
    const reminder = {
        text: reminderText,
        time: reminderDate
    };

    addReminder(reminder);
    saveReminder(reminder);

    // Limpar formulário
    reminderInput.value = '';
    reminderTime.value = '';
});

// Função para adicionar lembrete à lista
function addReminder(reminder) {
    const li = document.createElement('li');
    li.innerHTML = `<span>${reminder.text} - ${reminder.time}</span> <button onclick="removeReminder(this)">Remover</button>`;
    reminderList.appendChild(li);
}

// Função para remover lembrete
function removeReminder(button) {
    const li = button.parentElement;
    const reminderText = li.firstChild.textContent;

    // Remover do localStorage
    removeReminderFromStorage(reminderText);

    // Remover da UI
    li.remove();
}

// Função para salvar lembrete no localStorage
function saveReminder(reminder) {
    let reminders = localStorage.getItem('reminders') ? JSON.parse(localStorage.getItem('reminders')) : [];
    reminders.push(reminder);
    localStorage.setItem('reminders', JSON.stringify(reminders));
}

// Função para carregar lembretes do localStorage
function loadReminders() {
    let reminders = localStorage.getItem('reminders') ? JSON.parse(localStorage.getItem('reminders')) : [];
    reminders.forEach(addReminder);
}

// Função para remover lembrete do localStorage
function removeReminderFromStorage(reminderText) {
    let reminders = JSON.parse(localStorage.getItem('reminders'));
    reminders = reminders.filter(reminder => !reminderText.includes(reminder.text));
    localStorage.setItem('reminders', JSON.stringify(reminders));
}
