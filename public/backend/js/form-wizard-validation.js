/**
 *  Form Wizard - Fixed Version
 */

'use strict';

(function () {
  const select2 = $('.select2'),
    selectPicker = $('.selectpicker');

  // Wizard Validation
  // --------------------------------------------------------------------
  const wizardValidation = document.querySelector('#wizard-validation');
  if (typeof wizardValidation !== 'undefined' && wizardValidation !== null) {
    // Wizard form
    const wizardValidationForm = wizardValidation.querySelector('#wizard-validation-form');
    // Wizard steps
    const wizardValidationFormStep1 = wizardValidationForm.querySelector('#account-details-validation');
    const wizardValidationFormStep2 = wizardValidationForm.querySelector('#personal-info-validation');
    const wizardValidationFormStep3 = wizardValidationForm.querySelector('#social-links-validation');
    const wizardValidationFormStep4 = wizardValidationForm.querySelector('#account-section-validation');
    // Wizard next prev button
    const wizardValidationNext = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
    const wizardValidationPrev = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));

    const validationStepper = new Stepper(wizardValidation, {
      linear: true
    });

    // Account details - Step 1
    const FormValidation1 = FormValidation.formValidation(wizardValidationFormStep1, {
      fields: {
        formValidationphase: {
          validators: {
            notEmpty: {
              message: 'The Phase is required'
            },
            stringLength: {
              min: 1,
              max: 100,
              message: 'The phase must be between 1 and 100 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z0-9 ]+$/,
              message: 'The phase can only consist of alphabetical, number and space'
            }
          }
        },
        formValidationSession: {
          validators: {
            notEmpty: {
              message: 'The session is required'
            },
            stringLength: {
              min: 1,
              max: 100,
              message: 'The session must be between 1 and 100 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z0-9 ]+$/,
              message: 'The session can only consist of alphabetical, number and space'
            }
          }
        },
        formValidationbatch: {
          validators: {
            notEmpty: {
              message: 'The batch is required'
            },
            stringLength: {
              min: 1,
              max: 100,
              message: 'The batch must be between 1 and 100 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z0-9 ]+$/,
              message: 'The batch can only consist of alphabetical, number and space'
            }
          }
        },
        formValidationUsername: {
          validators: {
            notEmpty: {
              message: 'The username is required'
            },
            stringLength: {
              min: 3,
              max: 100,
              message: 'The username must be between 3 and 100 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z0-9 ]+$/,
              message: 'The username can only consist of alphabetical, number and space'
            }
          }
        },
        formValidationfathername: {
          validators: {
            notEmpty: {
              message: 'The father name is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The father name must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The father name can only consist of alphabetical characters and space'
            }
          }
        },
        formValidationmothername: {
          validators: {
            notEmpty: {
              message: 'The mother name is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The mother name must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The mother name can only consist of alphabetical characters and space'
            }
          }
        },
        formValidationphone: {
          validators: {
            notEmpty: {
              message: 'The phone number is required'
            },
            stringLength: {
              min: 11,
              max: 11,
              message: 'The phone number must be exactly 11 digits long'
            },
            regexp: {
              regexp: /^01[0-9]{9}$/,
              message: 'The phone number must start with "01" and contain only digits'
            }
          }
        },
        formValidationEmail: {
          validators: {
            notEmpty: {
              message: 'The Email is required'
            },
            emailAddress: {
              message: 'The value is not a valid email address'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    }).on('core.form.valid', function () {
      validationStepper.next();
    });

    // Personal info - Step 2
    const FormValidation2 = FormValidation.formValidation(wizardValidationFormStep2, {
      fields: {
        formValidationlocalguardian: {
          validators: {
            notEmpty: {
              message: 'The local guardian name is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The local guardian name must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The local guardian name can only consist of alphabetical characters and space'
            }
          }
        },
        formValidationLocalguardianphone: {
          validators: {
            notEmpty: {
              message: 'The local guardian phone is required'
            },
            stringLength: {
              min: 11,
              max: 11,
              message: 'The phone number must be exactly 11 digits long'
            },
            regexp: {
              regexp: /^01[0-9]{9}$/,
              message: 'The phone number must start with "01" and contain only digits'
            }
          }
        },
        formValidationdepartment: {
          validators: {
            notEmpty: {
              message: 'The department is required'
            }
          }
        },
        formValidationdatedfdirth: {
          validators: {
            notEmpty: {
              message: 'The date of birth is required'
            }
          }
        },
        formValidationplaceofbirth: {
          validators: {
            notEmpty: {
              message: 'The place of birth is required'
            }
          }
        },
        formValidationbloodgroup: {
          validators: {
            notEmpty: {
              message: 'The blood group is required'
            },
            stringLength: {
              min: 1,
              max: 10,
              message: 'The blood group must be between 1 and 10 characters long'
            }
          }
        },
        formValidationReligion: {
          validators: {
            notEmpty: {
              message: 'The religion is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The religion must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The religion can only consist of alphabetical characters and space'
            }
          }
        },
        formValidationGender: {
          validators: {
            notEmpty: {
              message: 'The gender is required'
            }
          }
        },
        formValidationNationality: {
          validators: {
            notEmpty: {
              message: 'The nationality is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The nationality must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The nationality can only consist of alphabetical characters and space'
            }
          }
        },
        formValidationssCountry: {
          validators: {
            notEmpty: {
              message: 'The country is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The country must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The country can only consist of alphabetical characters and space'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      validationStepper.next();
    });

    // Bootstrap Select handling
    if (selectPicker.length) {
      selectPicker.each(function () {
        var $this = $(this);
        $this.selectpicker().on('change', function () {
          FormValidation2.revalidateField('formValidationLanguage');
        });
      });
    }

    // Select2 handling
    if (select2.length) {
      select2.each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this
          .select2({
            placeholder: 'Select an option',
            dropdownParent: $this.parent()
          })
          .on('change', function () {
            // Revalidate the field when an option is chosen
            var fieldName = $this.attr('name');
            if (fieldName) {
              if (FormValidation1 && FormValidation1.getFields()[fieldName]) {
                FormValidation1.revalidateField(fieldName);
              }
              if (FormValidation2 && FormValidation2.getFields()[fieldName]) {
                FormValidation2.revalidateField(fieldName);
              }
              if (FormValidation3 && FormValidation3.getFields()[fieldName]) {
                FormValidation3.revalidateField(fieldName);
              }
            }
          });
      });
    }

    // Social links - Step 3
    const FormValidation3 = FormValidation.formValidation(wizardValidationFormStep3, {
      fields: {
        formValidationdivision: {
          validators: {
            notEmpty: {
              message: 'The division is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The division must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The division can only consist of alphabetical characters and space'
            }
          }
        },
        formValidationDistrict: {
          validators: {
            notEmpty: {
              message: 'The district is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The district must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The district can only consist of alphabetical characters and space'
            }
          }
        },
        formValidationPostcode: {
          validators: {
            notEmpty: {
              message: 'The post code is required'
            },
            stringLength: {
              min: 3,
              max: 10,
              message: 'The post code must be between 3 and 10 characters long'
            },
          }
        },
        formValidationPulishstation: {
          validators: {
            notEmpty: {
              message: 'The police station is required'
            },
            stringLength: {
              min: 0,
              max: 100,
              message: 'The police station must be between 3 and 50 characters long'
            },
          }
        },
        formValidationpostoffice: {
          validators: {
            notEmpty: {
              message: 'The post office is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The post office must be between 3 and 50 characters long'
            },
          }
        },
        formValidationVillage: {
          validators: {
            notEmpty: {
              message: 'The village is required'
            },
            stringLength: {
              min: 3,
              max: 50,
              message: 'The village must be between 3 and 50 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z ]+$/,
              message: 'The village can only consist of alphabetical characters and space'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      validationStepper.next();
    });

    // Final step - Step 4
    const FormValidation4 = FormValidation.formValidation(wizardValidationFormStep4, {
      fields: {
          formValidationEmail: {
          validators: {
            notEmpty: {
              message: 'The Email is required'
            },
            emailAddress: {
              message: 'The value is not a valid email address'
            }
          }
        },
           formValidationPass: {
          validators: {
            notEmpty: {
              message: 'The password is required'
            }
          }
        },
        formValidationConfirmPass: {
          validators: {
            notEmpty: {
              message: 'The Confirm Password is required'
            },
            identical: {
              compare: function () {
                return wizardValidationFormStep1.querySelector('[name="formValidationPass"]').value;
              },
              message: 'The password and its confirm are not the same'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      // Final form submission
      alert('Form submitted successfully!');
      // You can add your form submission logic here
      // wizardValidationForm.submit();
    });

    // Next button handlers
    wizardValidationNext.forEach(item => {
      item.addEventListener('click', event => {
        event.preventDefault();

        switch (validationStepper._currentIndex) {
          case 0:
            FormValidation1.validate();
            break;
          case 1:
            FormValidation2.validate();
            break;
          case 2:
            FormValidation3.validate();
            break;
          case 3:
            FormValidation4.validate();
            break;
          default:
            break;
        }
      });
    });

    // Previous button handlers
    wizardValidationPrev.forEach(item => {
      item.addEventListener('click', event => {
        event.preventDefault();
        
        switch (validationStepper._currentIndex) {
          case 3:
          case 2:
          case 1:
            validationStepper.previous();
            break;
          case 0:
          default:
            break;
        }
      });
    });
  }
})();