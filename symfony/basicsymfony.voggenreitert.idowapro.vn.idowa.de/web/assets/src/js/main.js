+function ($) {
    // variables
    var searchState  = false;
    var isUserLoading = false;
    var isUserEnd     = false;
    var userList;
    var searchInput, searchSubmit, searchUrl, winHeight, docHeight, userLoadUrl;

    $(document).ready(function () {
        userList = $('#user-list');

        // admin delete user
        $('#delete-user').click(function () {
            // check if there are check users
            var checkedUsers = userList.find('input[type="checkbox"]:checked');
            if (checkedUsers.length > 0) {
                // ask if users should really be deleted
                // then get userids and delete them
                var text = checkedUsers.length > 1 ? 'Möchten Sie die ausgewählten '+checkedUsers.length+' Benutzer wirklich löschen?' : 'Möchten Sie den ausgewählten Benutzer wirklich löschen?';
                var opt = {
                    type: 'userDelete',
                    title: 'Bentuzer löschen',
                    text: text,
                    button: ['löschen', 'abbrechen'],
                    callBack: userDeleteCallback,
                    show: true
                };
                createDialog(opt);
            } else {
                // show error message
                // no user is selected
                var opt = {
                    type: 'userDeleteWarning',
                    title: 'Fehler',
                    text: 'Es wurden keine Benutzer ausgewählt',
                    button: ['weiter'],
                    show: true
                };
                createDialog(opt);
            }
        });


        // admin serach user
        searchInput  = $('#search-user');
        searchSubmit = $('#search-user-submit');
        searchUrl    = searchSubmit.data('searchurl');

        // search-inputfield keyup event
        searchInput.keyup(function (e) {
            prepareSearchUsers($(this).val());
        });
        // search-submit button
        searchSubmit.click(function () {
            prepareSearchUsers(searchInput.val());
        });


        // admin user lazyload
        userLoadUrl = userList.data('load');

        winHeight = window.innerHeight;
        docHeight = document.body.offsetHeight;
        var lastScrollPos = 0;
        window.onscroll = function (e) {
            var scroll = window.scrollY;
            if (scroll > lastScrollPos) {
                if (winHeight + window.scrollY >= docHeight) {
                    loadMoreUsers();
                }
            }
            lastScrollPos = scroll;
        };

        // window resize
        $(window).resize(function () {
            winHeight = this.innerHeight;
            docHeight = document.body.offsetHeight;
        });


        // admin user multiselect checkboxes
        var lastChecked = null;
        var chkboxes = userList.find('input[type="checkbox"]');
        var labels = userList.find('label');
        labels.click(function (e) {
            var box = $(this).parent().children('input')[0];
            if(!lastChecked) {
                console.log('init');
                lastChecked = box;
                return;
            }
            if (e.shiftKey) {
                var start = chkboxes.index(box);
                var end = chkboxes.index(lastChecked);
                chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);
                // TODO: improve this!
            }
            lastChecked = box;
        });

        // admin user show search-box sm
        $('#show-search-user').click(function (e) {
            e.preventDefault();
            $('#user-search-box').slideToggle(200);
        });
    });

    // delete user callback
    function userDeleteCallback() {
        var userIds = [];
        var checkedUsers = userList.find('input[type="checkbox"]:checked');
        checkedUsers.each(function () {
            userIds.push($(this).data('user'));
        });
        $('#user_delete_form_selectedusers').val(userIds);
        $('form[name="user_delete_form"]').submit();
    }

    // check searchString and searchState
    function prepareSearchUsers(searchString) {
        searchString = searchString.trim();
        if (searchString.length > 2) {
            searchState = true;
            serachUsers(searchString);
        } else {
            if (searchState && searchString.length === 0) {
                searchState = false;
                serachUsers(searchString);
            }
        }
    }

    // send the searchString to the server and get the rendered list-template
    function serachUsers(searchString) {
        $.ajax({
            url: searchUrl,
            data: {
                'search': searchString
            },
            type: 'POST',
            success: function (data) {
                if (!data['nochange']) {
                    userList.html(data);
                    // reset for lazyloading
                    docHeight = document.body.offsetHeight;
                    isUserEnd = false;
                }
            },
            error: function (data) {
                console.log('error');
                console.log(data);
            }
        });
    }

    function loadMoreUsers() {
        if (!isUserLoading && !isUserEnd) {
            isUserLoading = true;
            var offset = userList.find('li').length - 1;
            $.ajax({
                url: userLoadUrl,
                type: 'POST',
                data: {
                    'offset': offset
                },
                success: function (data) {
                    if (data['end']) {
                        isUserEnd = true;
                    } else {
                        userList.append(data);
                        docHeight = document.body.offsetHeight;
                    }
                    isUserLoading = false;
                },
                error: function (data) {
                    console.log('error');
                    console.log(data);
                }
            });
        }
    }

    // TODOD dialog as object?

    var dialogType = null;

    function createDialog(dialog) {
        var d = $('#dialog');
        if (dialogType !== dialog.type) {
            dialogType = dialog.type;
            d.find('#dialogLabel').text(dialog.title);
            d.find('#dialogText').text(dialog.text);
            if (dialog.button.length > 1) {
                d.find('#modal-button1').show();
                d.find('#modal-button1').text(dialog.button[0]);
                d.find('#modal-button2').text(dialog.button[1]);
            } else {
                d.find('#modal-button2').text(dialog.button[0]);
                d.find('#modal-button1').hide();
            }
            if (dialog.callBack) {
                $('#modal-button1').click(function (e) {
                    dialog.callBack.apply(undefined, [e, d, dialog.paramCallBack]);
                });
            } else {
                d.modal('hide');
            }
        }
        if (dialog.show) {
            d.modal('show');
        }
    }
}(jQuery);