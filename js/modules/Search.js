import $ from 'jquery';

class Search {
  constructor(){
    this.openButton = $('.js-search-trigger');
    this.closeButton = $('.search-overlay__close');
    this.searchOverlay = $('.search-overlay');
    this.events();
    this.isOverlayOpen = false;
  }

  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on('click', this.closeOverlay.bind(this));
    $(document).on('keyup', this.keyPressDispacher.bind(this))
  }

  keyPressDispacher(e) {
    let key = e.keyCode;
    if(key == 83 && !this.isOverlayOpen) this.openOverlay();
    if(key == 27 && this.isOverlayOpen) this.closeOverlay();
  }

  openOverlay() {
    this.searchOverlay.addClass('search-overlay--active');
    $('body').addClass('body-no-scroll');
    console.log('OPEN');
    this.isOverlayOpen = true;
  }

  closeOverlay() {
    this.searchOverlay.removeClass('search-overlay--active');
    $('body').removeClass('body-no-scroll');
    console.log('Close');
    this.isOverlayOpen = false;
  }

}

export default Search;