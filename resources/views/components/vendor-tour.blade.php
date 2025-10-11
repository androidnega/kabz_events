@props(['autoStart' => false])

{{-- Shepherd.js Tour for Vendors --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@11.2.0/dist/css/shepherd.css"/>
<style>
    .shepherd-element {
        z-index: 9999;
    }
    
    .shepherd-modal-overlay-container {
        z-index: 9998;
    }
    
    .shepherd-button {
        background: rgb(124 58 237);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .shepherd-button:hover {
        background: rgb(109 40 217);
    }
    
    .shepherd-button-secondary {
        background: rgb(229 231 235);
        color: rgb(55 65 81);
    }
    
    .shepherd-button-secondary:hover {
        background: rgb(209 213 219);
    }
    
    .shepherd-header {
        background: linear-gradient(135deg, rgb(124 58 237) 0%, rgb(147 51 234) 100%);
        color: white;
        padding: 1rem;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .shepherd-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
    }
    
    .shepherd-text {
        padding: 1.5rem 1rem;
        font-size: 0.875rem;
        line-height: 1.5;
        color: rgb(55 65 81);
    }
    
    .shepherd-footer {
        padding: 1rem;
        border-top: 1px solid rgb(229 231 235);
    }
    
    .shepherd-arrow {
        display: none !important;
    }
    
    .shepherd-element {
        max-width: 95%;
        width: 420px;
        border-radius: 0.75rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        background: white;
    }
    
    @media (max-width: 640px) {
        .shepherd-element {
            width: 90%;
        }
    }
    
    .shepherd-text {
        max-height: 60vh;
        overflow-y: auto;
        background: white;
    }
    
    .shepherd-modal-overlay-container {
        opacity: 1 !important;
    }
    
    .shepherd-modal-overlay-container.shepherd-modal-is-visible {
        opacity: 1 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/shepherd.js@11.2.0/dist/js/shepherd.min.js"></script>
<script data-auto-start="{{ $autoStart ? '1' : '0' }}">
document.addEventListener('DOMContentLoaded', function() {
    const scriptTag = document.currentScript;
    const shouldAutoStart = scriptTag.getAttribute('data-auto-start') === '1';
    
    const tour = new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            cancelIcon: {
                enabled: true
            },
            classes: 'shepherd-theme-custom',
            scrollTo: { behavior: 'smooth', block: 'center' }
        }
    });

    // Step 1: Welcome
    tour.addStep({
        id: 'welcome',
        title: 'Welcome to KABZS EVENT! 🎉',
        text: 'Let us show you around your vendor dashboard. This quick tour will help you get started with managing your event services business.',
        buttons: [
            {
                text: 'Skip Tour',
                classes: 'shepherd-button-secondary',
                action: tour.cancel
            },
            {
                text: 'Start Tour',
                action: tour.next
            }
        ]
    });

    // Step 2: Business Profile
    tour.addStep({
        id: 'profile',
        title: 'Your Business Profile',
        text: 'This shows your business name and verification status. Getting verified builds trust with clients!',
        attachTo: {
            element: '.bg-purple-50',
            on: 'bottom'
        },
        buttons: [
            {
                text: 'Back',
                classes: 'shepherd-button-secondary',
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });

    // Step 3: Statistics
    tour.addStep({
        id: 'stats',
        title: 'Your Performance Stats',
        text: 'Track your views, bookings, and ratings here. These numbers update in real-time as clients interact with your services.',
        attachTo: {
            element: '.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-4',
            on: 'bottom'
        },
        buttons: [
            {
                text: 'Back',
                classes: 'shepherd-button-secondary',
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });

    // Step 4: Services
    tour.addStep({
        id: 'services',
        title: 'Manage Your Services',
        text: 'Add and manage your event services here. Create detailed service listings with prices, descriptions, and photos to attract more clients.',
        attachTo: {
            element: 'a[href="{{ route("vendor.services.index") }}"]',
            on: 'right'
        },
        buttons: [
            {
                text: 'Back',
                classes: 'shepherd-button-secondary',
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });

    // Step 5: Verification
    tour.addStep({
        id: 'verification',
        title: 'Get Verified',
        text: 'Submit your business documents to get verified. Verified vendors get a blue checkmark and appear higher in search results!',
        attachTo: {
            element: 'a[href="{{ route("vendor.verification") }}"]',
            on: 'right'
        },
        buttons: [
            {
                text: 'Back',
                classes: 'shepherd-button-secondary',
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });

    // Step 6: Subscriptions
    tour.addStep({
        id: 'subscriptions',
        title: 'Subscription Plans',
        text: 'Upgrade your plan to unlock more features like unlimited services, priority support, and premium placement in search results.',
        attachTo: {
            element: 'a[href="{{ route("vendor.subscriptions") }}"]',
            on: 'right'
        },
        buttons: [
            {
                text: 'Back',
                classes: 'shepherd-button-secondary',
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });

    // Step 7: Messages
    tour.addStep({
        id: 'messages',
        title: 'Client Messages',
        text: 'Respond to client inquiries quickly here. Fast responses improve your reputation and increase bookings!',
        attachTo: {
            element: 'a[href="{{ route("messages.index") }}"]',
            on: 'right'
        },
        buttons: [
            {
                text: 'Back',
                classes: 'shepherd-button-secondary',
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });

    // Step 8: Profile Settings
    tour.addStep({
        id: 'profile-settings',
        title: 'Profile Settings',
        text: 'Update your business information, contact details, and location here. Keep your profile up to date for better client engagement.',
        attachTo: {
            element: 'a[href="{{ route("profile.edit") }}"]',
            on: 'right'
        },
        buttons: [
            {
                text: 'Back',
                classes: 'shepherd-button-secondary',
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            }
        ]
    });

    // Step 9: Final
    tour.addStep({
        id: 'complete',
        title: 'You\'re All Set! 🎊',
        text: 'That\'s it! You\'re ready to start managing your event services business. Need help? Look for the "Tutorial" option in settings to replay this tour anytime.',
        buttons: [
            {
                text: 'Finish Tour',
                action: function() {
                    tour.complete();
                    // Mark tour as completed
                    fetch('{{ route("vendor.tour.complete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).catch(err => console.error('Error marking tour complete:', err));
                }
            }
        ]
    });

    // Handle tour cancel
    tour.on('cancel', function() {
        if (confirm('Are you sure you want to skip the tour? You can always restart it from Settings → Tutorial.')) {
            fetch('{{ route("vendor.tour.complete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(err => console.error('Error marking tour complete:', err));
        } else {
            return false;
        }
    });

    // Auto-start tour if needed
    if (shouldAutoStart) {
        setTimeout(() => {
            tour.start();
        }, 500);
    }

    // Make tour globally accessible for manual trigger
    window.startVendorTour = function() {
        tour.start();
    };
});
</script>
@endpush

