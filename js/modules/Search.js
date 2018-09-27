import $ from "jquery";

class Search {
  constructor() {
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.resultDiv = $("#search-overlay__results");
    this.events();
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
  }

  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    $(document).on("keydown", this.keyPressDispacher.bind(this));
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }

  typingLogic() {
    //this prevents activate the search if the text is not changed for example when using arrow keys
    let currentVal = this.searchField.val();
    if (currentVal != this.previousValue) {
      clearTimeout(this.typingTimer);
    //If the search field is empty remove the content of the result div and hide the spinner
      if (currentVal) {
        if (!this.isSpinnerVisible) {
          this.resultDiv.html('<div class="spinner-loader"></div>');
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getReults.bind(this), 1000);
      } else {
        this.resultDiv.html("");
        this.isSpinnerVisible = false;
      }
    }
    this.previousValue = this.searchField.val();
  }

  getReults() {
    $.getJSON(universityData.root_url + '/wp-json/wp/v2/posts?search='+ this.searchField.val(), posts => {
      this.resultDiv.html(`
        <h2 class="search-overlay__section-title">General Information </h2>
        ${posts.length ? '<ul class="link-list min-list">' : '<p>No general information matched your search</p>'}
          ${posts.map(post => `<li><a href="${post.link}">${post.title.rendered}</a></li>`).join('')}
        ${posts.length ? '</ul>' : ''}
      `);
      this.isSpinnerVisible = false;
    });
  }

  keyPressDispacher(e) {
    let key = e.keyCode;
    //Evaluates if any input or textarea is focus to prevent open the searh when typing
    let isFocus = $('input, textarea').is(':focus'); 
    if (key == 83 && !this.isOverlayOpen && !isFocus) this.openOverlay();
    if (key == 27 && this.isOverlayOpen) this.closeOverlay();
  }

  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    this.isOverlayOpen = true;
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }

}

export default Search;
