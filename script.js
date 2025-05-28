// Filters booking cards based on form inputs
function applyFilters() {
  const date = document.getElementById('date')?.value;
  const roomType = document.getElementById('roomType')?.value;
  const capacity = document.getElementById('capacity')?.value;
  const roomCards = document.querySelectorAll('.room-card');

  roomCards.forEach(card => {
    const cardDate = card.dataset.time;
    const cardType = card.dataset.type;
    const cardCapacity = card.dataset.capacity;

    let match = true;
    if (date && !cardDate.includes(date.split('/').reverse().join('/'))) match = false;
    if (roomType && cardType !== roomType) match = false;
    if (capacity && cardCapacity !== capacity) match = false;

    card.style.display = match ? 'block' : 'none';
  });
}

// Placeholder booking interaction
function bookRoom(roomNumber) {
  alert(`Booking room ${roomNumber}...`);
}

// Reservation submission validation and alert
function submitReservation() {
  const roomType = document.getElementById('roomTypeReserve')?.value;
  const roomNumber = document.getElementById('roomNumber')?.value;
  const startDateTime = document.getElementById('startDateTime')?.value;
  const endDateTime = document.getElementById('endDateTime')?.value;
  const subjectActivity = document.getElementById('subjectActivity')?.value;
  const purpose = document.getElementById('purpose')?.value;
  const divisionOffice = document.getElementById('divisionOffice')?.value;

  if (
    !roomType ||
    !roomNumber ||
    !startDateTime ||
    !endDateTime ||
    !subjectActivity ||
    !purpose ||
    !divisionOffice
  ) {
    alert("Please fill all required fields");
    return;
  }

  fetch("reserve_handler.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      roomType,
      roomNumber,
      startDateTime,
      endDateTime,
      subjectActivity,
      purpose,
      divisionOffice
    })
  })
    .then(async response => {
      const contentType = response.headers.get("content-type");
      if (contentType && contentType.indexOf("application/json") !== -1) {
        return response.json();
      } else {
        const text = await response.text();
        throw new Error("Server returned non-JSON response:\n" + text);
      }
    })
    .then(result => {
      if (result.success) {
        alert("Reservation successful!");
        location.reload();
      } else {
        alert("Reservation failed: " + result.message);
      }
    })
    .catch(error => {
      console.error("Fetch error:", error);
      alert("Request failed: " + error.message);
    });
}

// Placeholder for viewing booking request details
function viewDetails(requestId) {
  alert(`Viewing details for request ${requestId}`);
}

// Account creation redirect based on role
function createAccount() {
  const email = document.getElementById('email')?.value;
  const password = document.getElementById('password')?.value;
  const role = localStorage.getItem('userRole');

  if (email && password && role) {
    alert(`Account created for ${email} as ${role}`);
    window.location.href = role === 'student' ? 'bookings1.php' : 'bookings2.php';
  } else {
    alert('Please fill all fields and select a role');
  }
}

// Google Sign-In JWT parsing
function handleCredentialResponse(response) {
  const responsePayload = parseJwt(response.credential);
  alert(`Signed in as ${responsePayload.name} (${responsePayload.email})`);

  const role = localStorage.getItem('userRole');
  if (role === 'student') {
    window.location.href = 'bookings1.php';
  } else if (role === 'admin') {
    window.location.href = 'bookings2.php';
  } else {
    window.location.href = 'index.php';
  }
}

// Helper to parse Google JWT
function parseJwt(token) {
  const base64Url = token.split('.')[1];
  const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  const jsonPayload = decodeURIComponent(
    atob(base64).split('').map(c =>
      '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
    ).join('')
  );
  return JSON.parse(jsonPayload);
}

// Role selection storage + redirect
function selectRole(role) {
  localStorage.setItem('userRole', role);
  window.location.href = 'login.php'; // changed from signup-login.php
}

// Updates link destinations based on user role
function updateLinks() {
  const bookingsLink = document.getElementById('bookings-link');
  if (!bookingsLink) return; // Stop early if it's not on this page
  const adminLink = document.getElementById('admin-link');
  const adminLinkLi = adminLink?.parentElement;
  const role = localStorage.getItem('userRole');

  if (bookingsLink) {
    if (role === 'student') {
      bookingsLink.href = 'bookings1.php';
      if (adminLinkLi) adminLinkLi.style.display = 'none';
    } else if (role === 'admin') {
      bookingsLink.href = 'bookings2.php';
      if (adminLinkLi) adminLinkLi.style.display = 'list-item';
    } else {
      bookingsLink.href = '#';
      if (adminLinkLi) {
        adminLinkLi.style.display = 'list-item';
        adminLink.href = '#';
        adminLink.style.pointerEvents = 'none';
        adminLink.style.opacity = '0.5';
      }
    }
  }
}

// Google Calendar visual layout mapping
function parseDateTime(dateStr, timeStr) {
  const months = {
    'January': '01', 'February': '02', 'March': '03', 'April': '04',
    'May': '05', 'June': '06', 'July': '07', 'August': '08',
    'September': '09', 'October': '10', 'November': '11', 'December': '12'
  };
  const [month, day, year] = dateStr.split(' ');
  const [startTime, , endTime] = timeStr.split(' - ');

  const start = new Date(`${year}-${months[month]}-${day.padStart(2, '0')} ${startTime}`);
  const end = new Date(`${year}-${months[month]}-${day.padStart(2, '0')} ${endTime}`);

  return { start: start.toISOString(), end: end.toISOString() };
}

// Optional: Populate calendar with booking items (requires FullCalendar setup)
function initializeCalendar() {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  const events = [];
  const bookingItems = document.querySelectorAll('.booking-item');

  bookingItems.forEach(item => {
    const roomName = item.querySelector('.room-name')?.textContent;
    const dateTime = item.querySelector('.booking-details p:nth-child(2)')?.textContent;
    if (!roomName || !dateTime) return;

    const [datePart, startTime, , endTime] = dateTime.split(' ');
    const { start, end } = parseDateTime(datePart, `${startTime} - ${endTime}`);
    const status = item.querySelector('button')?.textContent || 'UNKNOWN';

    let color = '#4285F4';
    if (status === 'PENDING') color = '#FFA500';
    if (status === 'APPROVED') color = '#228B22';

    events.push({ title: roomName, start, end, color, extendedProps: { status } });
  });

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: events,
    eventClick(info) {
      alert(`Booking: ${info.event.title}\nTime: ${info.event.start.toLocaleString()} - ${info.event.end.toLocaleString()}\nStatus: ${info.event.extendedProps.status}`);
    }
  });

  calendar.render();
}

// Approval actions for admin
function approveBooking(id) {
  alert(`Booking ${id} approved`);
}
function declineBooking(id) {
  alert(`Booking ${id} declined`);
}

// Run on page load
document.addEventListener('DOMContentLoaded', () => {
  updateLinks();
  initializeCalendar();

  // Generate time slot checkboxes if the container exists
  if (document.getElementById("timeSlots")) generateTimeSlotCheckboxes();

  // Home link dynamic redirect
  const homeLink = document.querySelector('a[href="index.php"]');
  if (homeLink) {
    homeLink.addEventListener('click', function (e) {
      const role = localStorage.getItem('userRole');
      if (role === 'student') {
        e.preventDefault();
        window.location.href = 'bookings1.php';
      } else if (role === 'admin') {
        e.preventDefault();
        window.location.href = 'bookings2.php';
      }
      // else, default behavior (role-selection.php)
    });
  }

  flatpickr("#dateReserve", {
    enableTime: true,
    noCalendar: false,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "F j, Y — h:i K",
    minDate: "today",
    minuteIncrement: 30
  });

  flatpickr("#dateTimeRange", {
    enableTime: true,
    dateFormat: "Y-m-d h:i K", // Example: 2025-05-27 11:30 AM
    mode: "range",
    minTime: "07:00",
    maxTime: "21:00",
    time_24hr: false // Set to true if you want 24-hour format
  });

  flatpickr("#startDateTime", {
    enableTime: true,
    dateFormat: "Y-m-d h:i K",
    minTime: "07:00",
    maxTime: "21:00",
    time_24hr: false
  });

  flatpickr("#endDateTime", {
    enableTime: true,
    dateFormat: "Y-m-d h:i K",
    minTime: "07:00",
    time_24hr: false
  });
});

function filterRooms() {
  const roomType = document.getElementById("roomTypeReserve").value;
  const roomNumber = document.getElementById("roomNumber");
  roomNumber.innerHTML = '<option value="">-- Select Room --</option>';

  const classrooms = ["B6", "R104", "R105", "Room1"];
  const labs = ["CL2", "CL3", "CL4", "PL1", "PL2"];

  let options = [];
  if (roomType === "classroom") {
    options = classrooms;
  } else if (roomType === "laboratory") {
    options = labs;
  }

  options.forEach(room => {
    const opt = document.createElement("option");
    opt.value = room;
    opt.textContent = room;
    roomNumber.appendChild(opt);
  });
}

// Fetch and render timeslots dynamically
fetch('available_time_slots.php')
  .then(res => res.json())
  .then(slots => {
    const container = document.getElementById("timeSlots");
    if (!container) return;
    container.innerHTML = "";

    slots.forEach(slot => {
      const wrapper = document.createElement("div");
      wrapper.className = "timeslot-entry";

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.name = "timeSlots[]";
      checkbox.value = slot.slotID;
      checkbox.id = `slot_${slot.slotID}`;

      const label = document.createElement("label");
      label.htmlFor = checkbox.id;
      label.textContent = slot.label;

      wrapper.appendChild(checkbox);
      wrapper.appendChild(label);
      container.appendChild(wrapper);
    });
  });

window.generateTimeSlotCheckboxes = function () {
  console.log("Time slot checkbox generator loaded!");
  const container = document.getElementById("timeSlots");
  if (!container) return;

  // Clear previous checkboxes if any
  container.innerHTML = "";

  const start = 7 * 60;  // 7:00 AM in minutes
  const end = 21 * 60;   // 9:00 PM in minutes

  for (let mins = start; mins < end; mins += 30) {
    const startHour = Math.floor(mins / 60);
    const startMin = mins % 60;
    const endHour = Math.floor((mins + 30) / 60);
    const endMin = (mins + 30) % 60;

    const format = (h, m) =>
      `${((h + 11) % 12 + 1)}:${m === 0 ? "00" : m}` +
      ` ${h < 12 ? "AM" : "PM"}`;

    const label = `${format(startHour, startMin)} - ${format(endHour, endMin)}`;

    const wrapper = document.createElement("div");
    wrapper.style.marginBottom = "4px";

    const checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.name = "timeSlots[]";
    checkbox.value = label;
    checkbox.id = `slot_${mins}`;

    const checkboxLabel = document.createElement("label");
    checkboxLabel.htmlFor = checkbox.id;
    checkboxLabel.textContent = label;

    wrapper.appendChild(checkbox);
    wrapper.appendChild(checkboxLabel);
    container.appendChild(wrapper);
  }
};

function toggleTimeSlotDropdown() {
  const list = document.getElementById("timeSlots");
  list.style.display = (list.style.display === "none" || list.style.display === "") ? "block" : "none";
}

// Hide dropdown when clicking outside
document.addEventListener('click', function (e) {
  const isDropdown = e.target.closest('.dropdown-field');
  if (!isDropdown) {
    const list = document.getElementById("timeSlots");
    if (list) list.style.display = "none";
  }
});

// Display selected values in the input field
document.addEventListener("change", function () {
  const selected = Array.from(document.querySelectorAll('input[name="timeSlots[]"]:checked'))
    .map(cb => cb.value);
  const display = document.getElementById("timeSlotDisplay");
  if (display) display.value = selected.join(", ");
});

function togglePassword() {
  const pwd = document.getElementById('password');
  if (pwd.type === 'password') {
    pwd.type = 'text';
  } else {
    pwd.type = 'password';
  }
}

function togglePasswordVisibility() {
  const passwordInput = document.getElementById("password");
  const eyeIcon = document.getElementById("eye-icon");
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    // Eye closed (line through)
    eyeIcon.innerHTML = `
      <ellipse cx="10" cy="10" rx="8" ry="5" fill="none" stroke="#888" stroke-width="2"/>
      <circle cx="10" cy="10" r="2" fill="#888"/>
      <line x1="4" y1="16" x2="16" y2="4" stroke="#888" stroke-width="2"/>
    `;
  } else {
    passwordInput.type = "password";
    // Eye open
    eyeIcon.innerHTML = `
      <ellipse cx="10" cy="10" rx="8" ry="5" fill="none" stroke="#888" stroke-width="2"/>
      <circle cx="10" cy="10" r="2" fill="#888"/>
    `;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const toggle = document.getElementById('toggleEquipment');
  const options = document.getElementById('equipmentOptions');
  const selectedText = document.getElementById('equipmentSelectedText');
  const checkboxes = options.querySelectorAll('input[type="checkbox"]');

  function updateSelectedText() {
    const selected = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.parentElement.textContent.trim());
    selectedText.textContent = selected.length ? selected.join(', ') : 'Select equipment...';
    selectedText.style.color = selected.length ? '#333' : '#888';
  }

  toggle.addEventListener('click', function(e) {
    options.classList.toggle('hidden');
    toggle.classList.toggle('active');
  });

  toggle.addEventListener('blur', function() {
    setTimeout(() => {
      options.classList.add('hidden');
      toggle.classList.remove('active');
    }, 150);
  });

  checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedText));
});
