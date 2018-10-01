import $ from 'jquery';

class MyNotes {
  constructor() {
    this.events();
  }

  events() {
    $('#my-notes').on('click', '.delete-note', this.deleteNote);
    $('#my-notes').on('click', '.edit-note', this.editNote.bind(this));
    $('#my-notes').on('click', '.update-note', this.updateNote.bind(this));
    $('.submit-note').on('click', this.createNote.bind(this));
  }

  //Methods
  editNote(e) {
    var thisNote = $(e.target).parents('li');
    if(thisNote.data('state') == 'editable') {
      this.makeNoteReadOnly(thisNote);
    } else {
      this.makeNoteEditable(thisNote);
    }
  }

  makeNoteEditable(thisNote) {
    thisNote.find('.edit-note').html('Cancel <i class="fa fa-times" aria-hidden="true"></i>')
    thisNote.find('.note-title-field, .note-body-field').removeAttr('readonly').addClass('note-active-field');
    thisNote.find('.update-note').addClass('update-note--visible');
    thisNote.data('state', 'editable');
  }

  makeNoteReadOnly(thisNote) {
    thisNote.find('.edit-note').html('Edit <i class="fa fa-pencil" aria-hidden="true"></i>')
    thisNote.find('.note-title-field, .note-body-field').attr('readonly', 'readonly').removeClass('note-active-field');
    thisNote.find('.update-note').removeClass('update-note--visible');
    thisNote.data('state', 'cancel');
  }

  deleteNote(e) {
    var thisNote = $(e.target).parents('li');
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)
      },
      url: `${universityData.root_url}/wp-json/wp/v2/note/${thisNote.data('id')}`,
      type: 'DELETE', 
      success: (res) => thisNote.slideUp(),
      error: (err) => console.log(err)
    });
  }

  updateNote(e) {
    var thisNote = $(e.target).parents('li');
    var ourUpdatedPost = {
      'title': thisNote.find('.note-title-field').val(),
      'content': thisNote.find('.note-body-field').val()
    }

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)
      },
      url: `${universityData.root_url}/wp-json/wp/v2/note/${thisNote.data('id')}`,
      type: 'POST', 
      data: ourUpdatedPost,
      success: (res) => this.makeNoteReadOnly(thisNote),
      error: (err) => console.log(err)
    })
  }

  createNote(e) {

    var ourNewPost = {
      'title': $('.new-note-title').val(),
      'content': $('.new-note-body').val(),
      'status': 'publish'
    }

    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)
      },
      url: `${universityData.root_url}/wp-json/wp/v2/note/`,
      type: 'POST', 
      data: ourNewPost,
      success: (res) => {
        $('.new-note-title, .new-note-body').val('');
        $(`
          <li data-id="${res.id}">
            <input readonly class="note-title-field" type="text" value="${res.title.raw}">
            <span class="edit-note">Edit <i class="fa fa-pencil" aria-hidden="true"></i></span>
            <span class="delete-note">Delete <i class="fa fa-trash-o" aria-hidden="true"></i></span>
            <textarea readonly class="note-body-field">${res.content.raw}</textarea>
            <span class="update-note btn btn--blue btn--small">Save <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
          </li>
        `).prependTo('#my-notes').hide().slideDown();
      },
      error: (err) => console.log(err)
    })
  }

}

export default MyNotes;