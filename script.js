let deleteTicketId = null;

// Menampilkan modal konfirmasi untuk menghapus tiket
function showConfirmationModal(ticketId, teamName, matchDate) {
    deleteTicketId = ticketId;
    const modal = document.getElementById("confirmation-modal");
    const message = document.getElementById("confirmation-message");
    message.innerHTML = `Apakah Anda yakin ingin menghapus tiket untuk pertandingan <b>${teamName}</b> pada <b>${matchDate}</b>?`;
    modal.style.display = "flex";
}

// Menutup modal konfirmasi
function closeConfirmationModal() {
    const modal = document.getElementById("confirmation-modal");
    modal.style.display = "none";
}

// Menampilkan modal edit untuk tiket sepak bola
function showEditModal(ticketId, teamName, matchDate, seatNumber, price) {
    document.getElementById("edit-ticket-id").value = ticketId;
    document.getElementById("team-name").value = teamName;
    document.getElementById("match-date").value = matchDate;
    document.getElementById("seat-number").value = seatNumber;
    document.getElementById("ticket-price").value = price;

    const modal = document.getElementById("edit-modal");
    modal.style.display = "flex";
}

// Menutup modal edit
function closeEditModal() {
    const modal = document.getElementById("edit-modal");
    modal.style.display = "none";
}

// Mengirimkan konfirmasi penghapusan tiket ke server (AJAX / API)
function deleteTicket() {
    if (deleteTicketId) {
        // Lakukan penghapusan tiket (misalnya menggunakan AJAX)
        alert("Tiket berhasil dihapus!");
        closeConfirmationModal();
    } else {
        alert("Tiket tidak ditemukan.");
    }
}

// Menyimpan perubahan tiket yang telah diedit
function saveTicketChanges() {
    const ticketId = document.getElementById("edit-ticket-id").value;
    const teamName = document.getElementById("team-name").value;
    const matchDate = document.getElementById("match-date").value;
    const seatNumber = document.getElementById("seat-number").value;
    const price = document.getElementById("ticket-price").value;

    // Lakukan pengiriman data tiket yang telah diedit ke server (misalnya menggunakan AJAX)
    alert(`Tiket untuk pertandingan ${teamName} pada ${matchDate} telah diperbarui.`);
    closeEditModal();
}
