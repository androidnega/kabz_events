function createVerificationWizard(businessName, userName, userEmail) {
    return {
        step: 1,
        submitting: false,
        selectedRegion: null,
        selectedDistrict: null,
        selectedTown: null,
        formData: {
            business_name: businessName,
            business_category: '',
            business_registration_number: '',
            business_description: '',
            years_in_operation: '',
            business_region: '',
            business_district: '',
            business_town: '',
            business_logo: null,
            contact_full_name: userName,
            contact_role: '',
            contact_phone: '',
            contact_email: userEmail,
            national_id_type: '',
            national_id_number: '',
            id_document: null,
            profile_picture: null,
            facebook: '',
            instagram: '',
            twitter: '',
            website_url: '',
            proof_of_events: [],
            reference_letter: null,
            verification_reason: '',
            details_confirmed: false,
            terms_agreed: false
        },
        
        onRegionChange() {
            this.selectedDistrict = null;
            this.selectedTown = null;
            this.formData.business_district = '';
            this.formData.business_town = '';
        },
        
        onDistrictChange() {
            this.selectedTown = null;
            this.formData.business_town = '';
        },
        
        nextStep() {
            if (this.validateCurrentStep()) {
                this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        
        validateCurrentStep() {
            if (this.step === 1) {
                if (!this.formData.business_category) {
                    alert('Please select a business category');
                    return false;
                }
                if (!this.formData.business_description || this.formData.business_description.length < 50) {
                    alert('Business description must be at least 50 characters');
                    return false;
                }
                if (!this.formData.years_in_operation) {
                    alert('Please enter years in operation');
                    return false;
                }
                if (!this.formData.business_region || !this.formData.business_district || !this.formData.business_town) {
                    alert('Please select your complete business location (Region, District, and Town)');
                    return false;
                }
                if (!this.formData.business_logo) {
                    alert('Please upload your business logo');
                    return false;
                }
            } else if (this.step === 2) {
                if (!this.formData.contact_full_name) {
                    alert('Please enter your full name');
                    return false;
                }
                if (!this.formData.contact_role) {
                    alert('Please enter your role/position');
                    return false;
                }
                if (!this.formData.contact_phone) {
                    alert('Please enter your phone number');
                    return false;
                }
                if (!this.formData.contact_email) {
                    alert('Please enter your email address');
                    return false;
                }
                if (!this.formData.national_id_type) {
                    alert('Please select ID type');
                    return false;
                }
                if (!this.formData.national_id_number) {
                    alert('Please enter ID number');
                    return false;
                }
                if (!this.formData.id_document) {
                    alert('Please upload your ID card');
                    return false;
                }
                if (!this.formData.profile_picture) {
                    alert('Please upload your profile picture');
                    return false;
                }
            }
            return true;
        },
        
        handleMultipleFiles(event, fieldName) {
            const files = Array.from(event.target.files).slice(0, 5);
            this.formData[fieldName] = files;
        },
        
        async submitForm() {
            if (!this.formData.verification_reason) {
                alert('Please provide a reason for verification');
                return;
            }
            if (!this.formData.details_confirmed) {
                alert('Please confirm that all details are accurate');
                return;
            }
            if (!this.formData.terms_agreed) {
                alert('Please agree to the terms and conditions');
                return;
            }
            
            this.submitting = true;
            const formDataToSend = new FormData();
            
            for (let key in this.formData) {
                if (this.formData[key] !== null && this.formData[key] !== '' && !Array.isArray(this.formData[key]) && !(this.formData[key] instanceof File)) {
                    formDataToSend.append(key, this.formData[key]);
                }
            }
            
            if (this.formData.business_logo) formDataToSend.append('business_logo', this.formData.business_logo);
            if (this.formData.id_document) formDataToSend.append('id_document', this.formData.id_document);
            if (this.formData.profile_picture) formDataToSend.append('profile_picture', this.formData.profile_picture);
            if (this.formData.reference_letter) formDataToSend.append('reference_letter', this.formData.reference_letter);
            
            if (this.formData.proof_of_events && this.formData.proof_of_events.length > 0) {
                this.formData.proof_of_events.forEach((file, index) => {
                    formDataToSend.append(`proof_of_events[${index}]`, file);
                });
            }
            
            const socialLinks = {
                facebook: this.formData.facebook,
                instagram: this.formData.instagram,
                twitter: this.formData.twitter
            };
            formDataToSend.append('social_links', JSON.stringify(socialLinks));
            formDataToSend.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            try {
                const response = await fetch(window.verificationStoreUrl, {
                    method: 'POST',
                    body: formDataToSend
                });
                
                if (response.ok) {
                    window.location.href = window.verificationIndexUrl;
                } else {
                    const data = await response.json();
                    alert(data.message || 'An error occurred. Please try again.');
                    this.submitting = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                this.submitting = false;
            }
        }
    };
}

