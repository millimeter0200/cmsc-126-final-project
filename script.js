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

function bookRoom(roomNumber) {
  alert(`Booking room ${roomNumber}...`);
  // Add booking logic here
}

function submitReservation() {
  const roomType = document.getElementById('roomTypeReserve')?.value;
  const roomNumber = document.getElementById('roomNumber')?.value;
  const date = document.getElementById('dateReserve')?.value;
  const timeSlot = document.getElementById('timeSlot')?.value;
  const notes = document.getElementById('notes')?.value;

  if (roomType && roomNumber && date && timeSlot) {
    alert(`Reservation submitted for ${roomNumber} on ${date} from ${timeSlot}`);
    // Add reservation logic here
  } else {
    alert('Please fill all required fields');
  }
}

function viewDetails(requestId) {
  alert(`Viewing details for request ${requestId}`);
  // Add details view logic here
}

function createAccount() {
  const email = document.getElementById('email')?.value;
  const password = document.getElementById('password')?.value;
  const role = localStorage.getItem('userRole');
  if (email && password && role) {
    alert(`Account created for ${email} as ${role}`);
    // Redirect to the appropriate bookings page after account creation
    if (role === 'student') {
      window.location.href = 'bookings1.html';
    } else if (role === 'admin') {
      window.location.href = 'bookings2.html';
    }
  } else {
    alert('Please fill all fields and select a role');
  }
}

// Handle Google Sign-In response
function handleCredentialResponse(response) {
  // Decode the JWT ID token
  const responsePayload = parseJwt(response.credential);

  // Log user info (for debugging)
  console.log("ID: " + responsePayload.sub);
  console.log('Full Name: ' + responsePayload.name);
  console.log('Email: ' + responsePayload.email);

  // Here you can send the user info to your backend or redirect
  alert(`Signed in as ${responsePayload.name} (${responsePayload.email})`);
  const role = localStorage.getItem('userRole');
  if (role === 'student') {
    window.location.href = 'bookings1.html';
  } else if (role === 'admin') {
    window.location.href = 'bookings2.html';
  } else {
    window.location.href = 'role-selection.html'; // Redirect back to role selection if no role is set
  }
}

// Helper function to decode JWT token
function parseJwt(token) {
  const base64Url = token.split('.')[1];
  const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));

  return JSON.parse(jsonPayload);
}

// Select role and redirect to signup-login page
function selectRole(role) {
  localStorage.setItem('userRole', role);
  window.location.href = 'signup-login.html';
}

// Update navigation links based on selected role
function updateLinks() {
  const role = localStorage.getItem('userRole');
  const bookingsLink = document.getElementById('bookings-link');
  const adminLinkLi = document.getElementById('admin-link')?.parentElement;

  if (role === 'student') {
    bookingsLink.href = 'bookings1.html';
    // Hide the Admin link entirely for students
    if (adminLinkLi) {
      adminLinkLi.style.display = 'none';
    }
  } else if (role === 'admin') {
    bookingsLink.href = 'bookings2.html';
    if (adminLinkLi) {
      adminLinkLi.style.display = 'list-item';
      const adminLink = document.getElementById('admin-link');
      adminLink.href = 'bookings2.html';
    }
  } else {
    bookingsLink.href = '#';
    if (adminLinkLi) {
      adminLinkLi.style.display = 'list-item';
      const adminLink = document.getElementById('admin-link');
      adminLink.href = '#';
      adminLink.style.pointerEvents = 'none';
      adminLink.style.opacity = '0.5';
    }
  }
}

// Helper function to parse date and time into ISO format for FullCalendar
function parseDateTime(dateStr, timeStr) {
  const months = {
    'January': '01', 'February': '02', 'March': '03', 'April': '04', 'May': '05', 'June': '06',
    'July': '07', 'August': '08', 'September': '09', 'October': '10', 'November': '11', 'December': '12'
  };
  const [month, day, year] = dateStr.split(' ');
  const [startTime, , endTime] = timeStr.split(' - ');
  const start = new Date(`${year}-${months[month]}-${day.padStart(2, '0')} ${startTime}`);
  const end = new Date(`${year}-${months[month]}-${day.padStart(2, '0')} ${endTime}`);
  return { start: start.toISOString(), end: end.toISOString() };
}

// Initialize FullCalendar with bookings
function initializeCalendar() {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return; // Exit if calendar element is not found

  const events = [];
  const bookingItems = document.querySelectorAll('.booking-item');

  if (!bookingItems.length) {
    console.warn('No booking items found to populate the calendar.');
    return;
  }

  bookingItems.forEach((item, index) => {
    const roomNameEl = item.querySelector('.room-name');
    const dateTimeEl = item.querySelector('.booking-details p:nth-child(2)');
    if (!roomNameEl || !dateTimeEl) return; // Skip if elements are missing

    const roomName = roomNameEl.textContent;
    const dateTime = dateTimeEl.textContent;
    const [date, time] = dateTime.split(' ', 3);
    const dateStr = `${date} ${time}`;
    const timeStr = dateTime.split(' ').slice(3).join(' ');
    const { start, end } = parseDateTime(dateStr, timeStr);

    // Determine event color based on status
    const statusButton = item.querySelector('button');
    const status = statusButton ? statusButton.textContent : 'UNKNOWN';
    let color = '#3788d8'; // Default color
    if (status === 'PENDING') color = '#FFA500'; // Orange for pending
    if (status === 'CONFIRMED') color = '#228B22'; // Green for confirmed

    events.push({
      title: roomName,
      start: start,
      end: end,
      color: color,
      extendedProps: {
        status: status
      }
    });
  });

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: events,
    eventClick: function(info) {
      // Only allow clicking if user is signed in
      const userRole = localStorage.getItem('userRole');
      if (!userRole) {
        alert('Please sign up or log in to view booking details.');
        return;
      }
      alert(`Booking: ${info.event.title}\nTime: ${info.event.start.toLocaleString()} - ${info.event.end.toLocaleString()}\nStatus: ${info.event.extendedProps.status}`);
    }
  });
  calendar.render();
}

// On page load, update navigation links and initialize calendar
document.addEventListener('DOMContentLoaded', () => {
  updateLinks();
  initializeCalendar();
});

// Admin functions to approve or decline bookings
function approveBooking(bookingId) {
  alert(`Booking ${bookingId} approved`);
  // Add logic to update booking status to "Confirmed"
}

function declineBooking(bookingId) {
  alert(`Booking ${bookingId} declined`);
  // Add logic to remove or mark booking as "Declined"
}