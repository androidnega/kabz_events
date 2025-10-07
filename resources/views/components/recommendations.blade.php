@props(['limit' => 6, 'title' => 'Recommended for you', 'showLocation' => true])

<div id="recommendations" class="mt-8">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">{{ $title }}</h3>
    <div id="rec-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @for ($i = 0; $i < $limit; $i++)
            <div class="bg-white rounded-xl shadow p-4 animate-pulse">
                <div class="h-36 bg-gray-200 rounded mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>
        @endfor
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const showLocation = {{ $showLocation ? 'true' : 'false' }};
    
    function fetchRecommendations(lat = null, lng = null) {
        const params = new URLSearchParams();
        if (lat && lng) {
            params.append('lat', lat);
            params.append('lng', lng);
        }
        params.append('limit', {{ $limit }});
        
        fetch('/recommendations?' + params.toString())
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('rec-list');
                list.innerHTML = '';
                
                if (!data || data.length === 0) {
                    list.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">No recommendations available at this time.</div>';
                    return;
                }
                
                data.forEach(v => {
                    const el = document.createElement('div');
                    el.className = 'bg-white rounded-xl shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden';
                    
                    // Get cover image or use placeholder
                    const coverImage = v.cover_image ?? '/images/vendor-placeholder.png';
                    
                    // Build rating stars
                    const rating = v.rating_cached ?? 0;
                    const fullStars = Math.floor(rating);
                    const halfStar = rating % 1 >= 0.5;
                    let starsHtml = '';
                    
                    for (let i = 0; i < 5; i++) {
                        if (i < fullStars) {
                            starsHtml += '<span class="text-yellow-400">★</span>';
                        } else if (i === fullStars && halfStar) {
                            starsHtml += '<span class="text-yellow-400">⯨</span>';
                        } else {
                            starsHtml += '<span class="text-gray-300">★</span>';
                        }
                    }
                    
                    el.innerHTML = `
                        <img src="${coverImage}" alt="${v.business_name}" class="w-full h-36 object-cover" onerror="this.src='/images/vendor-placeholder.png'" />
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-1 truncate">${v.business_name}</h4>
                            <div class="flex items-center mb-2">
                                <div class="text-sm">${starsHtml}</div>
                                <span class="text-xs text-gray-500 ml-2">(${rating.toFixed(1)})</span>
                            </div>
                            ${v.address ? `<p class="text-sm text-gray-600 mb-3 truncate">${v.address}</p>` : ''}
                            <a href="/vendors/${v.slug}" 
                               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition-colors duration-200"
                               onclick="logRecommendationClick(${v.id})">
                                View Details
                            </a>
                        </div>
                    `;
                    list.appendChild(el);
                });
            })
            .catch(error => {
                console.error('Failed to fetch recommendations:', error);
                const list = document.getElementById('rec-list');
                list.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">Unable to load recommendations. Please try again later.</div>';
            });
    }

    // Log recommendation click
    window.logRecommendationClick = function(vendorId) {
        @auth
        fetch(`/interactions/vendors/${vendorId}/recommendation-click`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ source: 'homepage' })
        }).catch(err => console.warn('Failed to log recommendation click:', err));
        @endauth
    };

    // Try to get browser geolocation (ask permission)
    if (showLocation && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                fetchRecommendations(position.coords.latitude, position.coords.longitude);
            },
            function(error) {
                // fallback without location
                console.log('Geolocation denied or unavailable, using fallback recommendations');
                fetchRecommendations();
            },
            {
                timeout: 5000,
                enableHighAccuracy: false
            }
        );
    } else {
        fetchRecommendations();
    }
});
</script>

