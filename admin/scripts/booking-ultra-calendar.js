jQuery(function ($) {

    let $calendar        = $('#full_calendar_wrapper .bup-calendar-element'),
        $staffPills      = $('.bupro-js-staff-pills'),
        $staffLinks      = $('.bup-calendar-staff-bar-filter li > a'),
        $staffFilter     = $('#bupro-js-staff-filter'),
        $servicesFilter  = $('#bupro-js-services-filter'),
        $locationsFilter = $('#bupro-js-locations-filter'),
        $gcSyncButton    = $('#bupro-google-calendar-sync'),
        $ocSyncButton    = $('#bupro-outlook-calendar-sync'),
        staffMembers     = [],
        staffIds         = getCookie('bupro_cal_st_ids'),
        serviceIds       = getCookie('bupro_cal_service_ids'),
        locationIds      = getCookie('bupro_cal_location_ids'),
        tabId            = getCookie('bupro_cal_tab_id'),
        lastView         = getCookie('bupro_cal_view'),
        headerToolbar    = {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek,timeGridDay,resourceTimeGridDay,listWeek'
        },
        calendarTimer    = null,
        calendar         = null;

    /**
     * Init tabs.
     */
    // Scrollable pills
    (function () {
        let down = false;
        let dragged = false;
        let scrollLeft = 0;
        let x = 0;
        let el = $staffPills.get(0);
        
       /* setTimeout(function() {
            el.scrollLeft = $staffLinks.filter('.active').position().left
        }, 0); */
        
        $staffPills.mousedown(function(e) {
            down = true;
            dragged = false;
            scrollLeft = this.scrollLeft;
            x = e.clientX;
        });
            
        $staffLinks.on('click', function (e) {
            if (dragged) {
                e.stopImmediatePropagation();
                e.preventDefault();
                dragged = false;
            }
        }).on('dragstart', function () {
            return false;
        });
        
        $('body').mousemove(function(e) {
            if (down) {
                el.scrollLeft = scrollLeft + x - e.clientX;
                if (Math.abs(scrollLeft - el.scrollLeft) > 2) {
                    dragged = true;
                }
            }
        }).mouseup(function() {
            down = false;
        });
    })();
    
    $staffLinks.on('click', function (e) {
        e.preventDefault();
        $staffLinks.removeClass('active');
        $(this).addClass('active');
        let staff_id = $(this).data('staff_id');
        setCookie('bupro_cal_tab_id', staff_id);
        if (staff_id == 0) {
            let view = calendar.ec.getOption('view');
            headerToolbar.end = 'dayGridMonth,timeGridWeek,resourceTimeGridDay,listWeek';
            calendar.ec
                .setOption('headerToolbar', headerToolbar)
                .setOption('view', view === 'timeGridDay' ? 'resourceTimeGridDay' : view)
                .refetchEvents()
            ;
        } else {
            let view = calendar.ec.getOption('view');
            headerToolbar.end = 'dayGridMonth,timeGridWeek,timeGridDay,listWeek';
            calendar.ec
                .setOption('headerToolbar', headerToolbar)
                .setOption('view', view === 'resourceTimeGridDay' ? 'timeGridDay' : view)
                .refetchEvents()
            ;
        }
    });
    
    $staffLinks.filter('[data-staff_id=' + tabId + ']').addClass('active');
    if ($staffLinks.filter('.active').length === 0) {
        $staffLinks.eq(0).addClass('active').parent().show();
    }

    /**
     * Init staff filter.
     */
   /* $staffFilter.buproDropdown({
        onChange: function (values, selected, all) {
            let ids = [];
            staffMembers = [];
            this.buproDropdown('getSelectedExt').forEach(function (item) {
                ids.push(item.value);
                staffMembers.push({id: item.value, title: encodeHTML(item.name)});
            });
            calendar.ec.setOption('resources', staffMembers);
            setCookie('bupro_cal_st_ids', ids);
            if (all) {
                $staffLinks.filter('[data-staff_id!=0]').parent().toggle(selected);
            } else {
                values.forEach(function (value) {
                    $staffLinks.filter('[data-staff_id=' + value + ']').parent().toggle(selected);
                });
            }
            if ($staffLinks.filter(':visible.active').length === 0) {
                $staffLinks.filter(':visible:first').triggerHandler('click');
            } else if ($staffLinks.filter('.active').data('staff_id') === 0) {
                calendar.ec.refetchEvents();
            }
        }
    });
    if (staffIds === null) {
        $staffFilter.buproDropdown('selectAll');
    } else if (staffIds !== '') {
        $staffFilter.buproDropdown('setSelected', staffIds.split(','));
    } else {
        $staffFilter.buproDropdown('toggle');
    }
    // Populate staffMembers.
    $staffFilter.buproDropdown('getSelectedExt').forEach(function (item) {
        staffMembers.push({id: item.value, title: encodeHTML(item.name)});
        $staffLinks.filter('[data-staff_id=' + item.value + ']').parent().show();
    });
    
    */
    
    
    jQuery(document).on("click", "#bup-btn-calendar-filter", function(e) {
			
			e.preventDefault();		
			jQuery("#bup-spinner").show();
			calendar.ec.refetchEvents();    		
				
    });	

    
    jQuery(document).on("click", ".bup-adm-change-appoint-status-opt", function(e) {
            
            e.preventDefault();     
                
            var appointment_id =  jQuery(this).attr("appointment-id");
            var appointment_status =  jQuery(this).attr("appointment-status"); 
            alert(appointment_id);
            
            jQuery("#bup-spinner").show();                      
                
            jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {"action": "bup_update_appo_status_ed", 
                           "appointment_id": appointment_id,
                           "appointment_status": appointment_status,
                           _ajax_nonce : bup_calendar.nonce
                           

                          },
                    
                    success: function(data){            
                        jQuery("#bup-app-status" ).html( data );
                        jQuery("#bup-td-status" ).html( data );
                        jQuery("#bup-spinner").hide();  
                        //$fullCalendar.fullCalendar( 'refetchEvents' );
                        calendar.ec.refetchEvents();                
                        jQuery("#bup-appointment-change-status" ).dialog( "close" );                                            
                                        
                                        
                                            

                        }
                });             
            
                
            
            e.stopPropagation(); 
                
    }); 
   

    /**
     * Init calendar refresh buttons.
     */
    function refreshBupProCalendar() {
        let $refresh = $('input[name="bupro_calendar_refresh_rate"]:checked');
        clearTimeout(calendarTimer);
        if ($refresh.val() > 0) {
            calendarTimer = setTimeout(function () {
                calendar.ec.refetchEvents();
            }, $refresh.val() * 1000)
        }
    }

    function encodeHTML(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    $('#bupro-calendar-refresh').on('click', function () {
        calendar.ec.refetchEvents();
    });

    $('input[name="bupro_calendar_refresh_rate"]').change(function () {
        $.post(
            ajaxurl,
            {action: 'bupro_update_calendar_refresh_rate', csrf_token: BuproL10n.csrf_token, rate: this.value},
            function (response) {},
            'json'
        );
        if (this.value > 0) {
            $(this).closest('.btn-group').find('button').addClass('btn-success').removeClass('btn-default');
        } else {
            $(this).closest('.btn-group').find('button').addClass('btn-default').removeClass('btn-success');
        }
        refreshBupProCalendar();
    });

    refreshBupProCalendar();

    // View buttons
    if ($staffLinks.filter('.active').data('staff_id') == 0) {
        headerToolbar.end = 'dayGridMonth,timeGridWeek,resourceTimeGridDay,listWeek';
        if (headerToolbar.end.indexOf(lastView) === -1) {
            lastView = 'resourceTimeGridDay';
        }
    } else {
        headerToolbar.end = 'dayGridMonth,timeGridWeek,timeGridDay,listWeek';
        if (headerToolbar.end.indexOf(lastView) === -1) {
            lastView = 'dayGridMonth';
        }
    }    
    if(lastView.length == 0) {
        lastView = 'dayGridMonth';
    }
    /**
     * Init Calendar.
     */
     if($calendar.length > 0) {
        calendar = new BupProCalendar($calendar, {
            calendar: {
                // General Display.
                headerToolbar: headerToolbar,
                height: heightEC(),
                // Views.
                view: lastView,
                views: {
                    resourceTimeGridDay: {
                        resources: staffMembers,
                        filterResourcesWithEvents: BuproL10n.filterStaffWithEvents,
                        titleFormat: {year: 'numeric', month: 'short', day: 'numeric', weekday: 'short'}
                    }
                }
            },
            getCurrentStaffId: function () {
                return $staffLinks.filter('.active').data('staff_id');
            },
            getStaffMemberIds: function () {
                
                let ids = [],
                    staffId = this.getCurrentStaffId()
                ;

                if (staffId == 0) {
                    staffMembers.forEach(function (staff) {
                        ids.push(staff.id);
                    });
                    
                } else {
                    ids.push(staffId);
                }

                return ids;
            },
            
                  
            getLocationIds: function () {            
                
                 let locationId = jQuery("#bup-location-calendar").val(); 
                
                if (locationId == '') {               
                                
                } else {
                    
                  
                }
                
                
                
               // return locationIds;
               return locationId;
            },
            
            getServiceIds: function () {
                return serviceIds;
            },
            refresh: refreshBupProCalendar,
            viewChanged: function (view) {
                setCookie('bupro_cal_view', view.type);
            },
            l10n: BuproL10n
        });
    }
    function heightEC() {
        let height = $(window).height() - $calendar.offset().top - 20;

        return (height > 620 ? height : 620) + 'px';
    }

    $(window).on('resize', function () {
        if( typeof calendar !== 'undefinded') {
          calendar.ec.setOption('height', heightEC());  
        }
        
    });

    /**
     * Set cookie.
     *
     * @param key
     * @param value
     */
    function setCookie(key, value) {
        var expires = new Date();
        expires.setTime(expires.getTime() + 86400000); // 60 × 60 × 24 × 1000
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
    }

    /**
     * Get cookie.
     *
     * @param key
     * @return {*}
     */
    function getCookie(key) {
        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    }

    /**
     * Sync with Google Calendar.
     */
    $gcSyncButton.on('click', function () {
        var ladda = Ladda.create(this);
        ladda.start();
        $.post(
            ajaxurl,
            {action: 'bupro_advanced_google_calendar_sync', csrf_token: BuproL10n.csrf_token},
            function (response) {
                if (response.success) {
                    calendar.ec.refetchEvents();
                }
                buproAlert(response.data.alert);
                ladda.stop();
            },
            'json'
        );
    });

    /**
     * Sync with Outlook Calendar.
     */
    $ocSyncButton.on('click', function () {
        var ladda = Ladda.create(this);
        ladda.start();
        $.post(
            ajaxurl,
            {action: 'bupro_outlook_calendar_sync', csrf_token: BuproL10n.csrf_token},
            function (response) {
                if (response.success) {
                    calendar.ec.refetchEvents();
                }
                buproAlert(response.data.alert);
                ladda.stop();
            },
            'json'
        );
    });
    
    
    
});