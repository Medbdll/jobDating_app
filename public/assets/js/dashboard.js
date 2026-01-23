// Store all announcements for filtering
let allAnnouncements = [];

// Initialize announcements from server-side data
function initializeAnnouncements(announcements) {
    allAnnouncements = announcements;
}

// AJAX Search functionality
function searchAnnouncements() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value;
    
    // Show loading state
    const announcementsList = document.getElementById('announcementsList');
    announcementsList.innerHTML = '<div class="col-span-full text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><p class="mt-2 text-gray-500">Recherche en cours...</p></div>';
    
    // Build query parameters
    const params = new URLSearchParams();
    if (searchInput) params.append('search', searchInput);
    if (typeFilter) params.append('type', typeFilter);
    
    // Make AJAX request
    fetch(`/student/dashboard/search?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateAnnouncementsList(data.announcements);
                updateAnnouncementCount(data.count);
            } else {
                announcementsList.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">Erreur lors de la recherche</div>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            announcementsList.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">Erreur de connexion</div>';
        });
}

// Filter announcements (now uses AJAX)
function filterAnnouncements() {
    searchAnnouncements();
}

// Update announcement count display
function updateAnnouncementCount(count) {
    const countElement = document.querySelector('.text-gray-500');
    if (countElement) {
        countElement.textContent = `${count} offres`;
    }
}

// Update announcements list in DOM
function updateAnnouncementsList(announcements) {
    const announcementsList = document.getElementById('announcementsList');
    
    if (announcements.length === 0) {
        announcementsList.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucune offre trouvée</h3>
                <p class="text-gray-500 mb-4">Essayez d'autres filtres ou termes de recherche</p>
            </div>
        `;
        return;
    }
    
    announcementsList.innerHTML = announcements.map(announcement => {
        const contractTypeLabel = announcement.contract_type === 'internship' ? 'Stage' : 'Emploi';
        const contractTypeClass = announcement.contract_type === 'internship' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
        
        return `
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6 border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">${announcement.title}</h3>
                        <p class="text-gray-600 mb-2">${announcement.company_name}</p>
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            ${announcement.location || 'Non spécifié'}
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${contractTypeClass}">
                        ${contractTypeLabel}
                    </span>
                </div>
                
                <p class="text-gray-700 mb-4 line-clamp-3">${announcement.description}</p>
                
                ${announcement.skills ? `
                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-700">Compétences requises:</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            ${announcement.skills.split(',').map(skill => 
                                `<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">${skill.trim()}</span>`
                            ).join('')}
                        </div>
                    </div>
                ` : ''}
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        ${new Date(announcement.created_at).toLocaleDateString('fr-FR')}
                    </span>
                    <a href="/student/dashboard/apply/${announcement.id}" 
                       class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-300">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Postuler
                    </a>
                </div>
            </div>
        `;
    }).join('');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initial load is handled by server-side rendering
    // No need to call filterAnnouncements() on load
});
