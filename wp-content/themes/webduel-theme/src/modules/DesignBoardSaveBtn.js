let $ = jQuery;
//Design board save button
window.productID = 0
window.productName = 0
class DesignBoardSaveBtn {
    constructor() {
        this.heartBtn = document.querySelectorAll('.design-board-save-btn-container');

        this.events();
    }
    //events
    events() {

        // show design board modal 
        $(this.heartBtn).on('click', this.showDesignBoardModal)
        // hide design board modal 
        $(document).on('click', '.design-board-selection-modal .footer-container .cancel', this.hideDesignBoardModal)
        // hide design board modal 
        $(document).on('click', '.design-board-selection-modal .fa-xmark', this.hideDesignBoardModal)
        // hide design board modal when clicked on black overlay 
        $(document).on('click', '.dark-overlay', this.hideDesignBoardModal)
        // add to board
        $(document).on('click', '.design-board-selection-modal .board-list .list-item .save-btn', this.addToBoard)

        // show create modal 
        $(document).on('click', '.create-board-container', this.showCreateBoardModal)
    }

    // show design board list modal 
    showDesignBoardModal(e) {
        $('.design-board-selection-modal').show()
        $('.dark-overlay').show()


        window.productID = $(this).attr('data-id')
        window.productName = $(this).attr('data-name')
    }

    // hide design board modal 
    hideDesignBoardModal() {
        $('.design-board-selection-modal').hide()
        $('.dark-overlay').hide()
        $('.create-board-modal').hide()
    }

    // add to board 
    addToBoard(e) {
        const boardID = $(e.target).attr('data-boardid')
        const boardPostStatus = $(e.target).attr('data-poststatus')

        $(e.target).html('<i class="fa-duotone fa-loader fa-spin"></i>')

        //add to board
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
            },
            url: inspiryData.root_url + '/wp-json/inspiry/v1/add-to-board',
            type: 'POST',
            data: {
                'boardID': boardID,
                'productID': window.productID,
                'postTitle': window.productName,
                'status': boardPostStatus
            },
            complete: () => {
                console.log('saved')
            },
            success: (response) => {
                console.log('this is a success area')
                if (response) {
                    console.log(response);
                    // $('.design-board-save-btn-container i').attr('data-exists', 'yes');
                    // //fill heart
                    // $('.design-board-save-btn-container i').addClass('fas fa-heart');
                    $(e.target).html('Saved')
                }
            },
            error: (response) => {
                console.log('this is an error');
                console.log(response)
                $(e.target).html('Error')
            }
        });
    }

    showCreateBoardModal(e) {
        $('.create-board-modal').show()
        $('.design-board-selection-modal').hide()
        // submit form 
        let boardName
        let boardStatus
        $('#create-board-form').submit((e) => {
            e.preventDefault()
            boardName = $('#board-name').val()
            boardStatus = $('#board-checkbox').is(":checked") ? 'private' : 'publish'
            // create board

            $(".create-board-modal form button").text('Creating')
            $.ajax({

                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
                },
                url: inspiryData.root_url + '/wp-json/inspiry/v1/manage-board',
                type: 'POST',
                data: {
                    'boardName': boardName,
                    'status': boardStatus,
                    'boardDescription': 'description is here'
                },
                complete: () => {
                    console.log('completed')
                },
                success: (response) => {
                    console.log(response);
                    if (response) {
                        let boardID = response
                        addToBoard(boardID, boardStatus)
                    }
                },
                error: (response) => {
                    console.log('this is an error');
                    console.log(response)
                    $('.create-board-modal form .error').text(response.responseText)
                    $(".create-board-modal form button").text('Create')
                }
            });
        })




        // add product to board after board is created 
        const addToBoard = (boardID, boardPostStatus) => {
            console.log(boardID)
            console.log(boardPostStatus)
            console.log(window.productID)
            console.log(window.productName)
            //add to board
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
                },
                url: inspiryData.root_url + '/wp-json/inspiry/v1/add-to-board',
                type: 'POST',
                data: {
                    'boardID': boardID,
                    'productID': window.productID,
                    'postTitle': window.productName,
                    'status': boardPostStatus
                },
                complete: () => {
                    console.log('saved')
                },
                success: (response) => {
                    console.log('this is a success area')
                    if (response) {
                        console.log(response);
                        $(".create-board-modal form button").text('Created')
                        location.reload()
                    }
                },
                error: (response) => {
                    console.log('this is an error');
                    console.log(response)
                    $(".create-board-modal form button").text('Create')
                    $('.create-board-modal form .error').text('Something went wrong')
                }
            });
        }


    }
}

export default DesignBoardSaveBtn;