// Kanban Board JavaScript for Drag and Drop
document.addEventListener('DOMContentLoaded', function() {
    const kanbanColumns = document.querySelectorAll('.kanban-column');
    const cards = document.querySelectorAll('.kanban-card');

    // Make cards draggable
    cards.forEach(card => {
        card.draggable = true;
        
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', card.dataset.complaintId);
            card.classList.add('opacity-50');
        });

        card.addEventListener('dragend', function() {
            card.classList.remove('opacity-50');
        });
    });

    // Make columns drop zones
    kanbanColumns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            column.classList.add('bg-gray-50');
        });

        column.addEventListener('dragleave', function() {
            column.classList.remove('bg-gray-50');
        });

        column.addEventListener('drop', function(e) {
            e.preventDefault();
            column.classList.remove('bg-gray-50');
            
            const complaintId = e.dataTransfer.getData('text/plain');
            const newStatus = column.dataset.status;
            const card = document.querySelector(`[data-complaint-id="${complaintId}"]`);
            
            if (card && card.closest('.kanban-column').dataset.status !== newStatus) {
                // Move card visually
                column.querySelector('.kanban-cards').appendChild(card);
                
                // Update status via AJAX
                updateComplaintStatus(complaintId, newStatus);
            }
        });
    });
});

function updateComplaintStatus(complaintId, newStatus) {
    const formData = new FormData();
    formData.append('status', newStatus);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

    fetch(`/admin/complaints/${complaintId}/status`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Status berhasil diupdate', 'success');
        } else {
            // Revert card position
            showNotification('Gagal mengupdate status', 'error');
            location.reload(); // Reload to sync state
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
        location.reload();
    });
}

function showNotification(message, type) {
    // Simple notification - can be enhanced with a toast library
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

