import $ from "jquery";

class Like {
  constructor() {
    this.events();
  }

  events() {
    $('.like-box').on('click', this.clickDispatcher.bind(this));
  }

  clickDispatcher(e) {
    var currentLikeBox = $(e.target).closest('.like-box');

    if(currentLikeBox.attr('data-exists') == 'yes') {
      this.deleteLike(currentLikeBox);
    } else {
      this.createLike(currentLikeBox);
    }
  }

  createLike(currentLikeBox) {
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)
      },
      url: `${universityData.root_url}/wp-json/university/v1/manageLike`,
      type: 'POST',
      data: {'professorId': currentLikeBox.data('professor')},
      success: (res) => {
        currentLikeBox.attr('data-exists', 'yes');
        var likeCount = parseInt(currentLikeBox.find('.like-count').html(), 10);
        likeCount++;
        currentLikeBox.find('.like-count').html(likeCount);
        currentLikeBox.attr('data-like', res)
        console.log(res)
      },
      error: (err) => console.log(err)
    })
  }

  deleteLike(currentLikeBox) {
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)
      },
      url: `${universityData.root_url}/wp-json/university/v1/manageLike`,
      data: {'like': currentLikeBox.attr('data-like')},
      type: 'DELETE',
      success: (res) => {
        currentLikeBox.attr('data-exists', 'no');
        var likeCount = parseInt(currentLikeBox.find('.like-count').html(), 10);
        likeCount--;
        currentLikeBox.find('.like-count').html(likeCount);
        currentLikeBox.attr('data-like', '')
      },
      error: (err) => console.log(err)
    })
  }

}

export default Like;