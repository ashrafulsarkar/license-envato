<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Shared "View" modal shell + JS — the Dashboard and Users pages both
 * `include` this once. Opens whichever <template id="le-license-detail-*">
 * a .le-view-license link points at via its data-target attribute.
 */
?>
<div id="le-view-modal" class="le-modal-overlay">
    <div class="le-modal-box">
        <button type="button" class="le-modal-close" aria-label="<?php esc_attr_e( 'Close', 'license-envato' ); ?>">&times;</button>
        <h2 id="le-view-modal-title"><?php esc_html_e( 'License details', 'license-envato' ); ?></h2>
        <div id="le-view-modal-body" autocomplete="off" data-lpignore="true" data-1p-ignore data-bwignore="true" data-form-type="other"></div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('le-view-modal');
    var body  = document.getElementById('le-view-modal-body');
    var title = document.getElementById('le-view-modal-title');
    var defaultTitle = <?php echo wp_json_encode( __( 'License details', 'license-envato' ) ); ?>;

    function openModal(targetId, titleText) {
        var tpl = document.getElementById(targetId);
        if (!tpl) {
            return;
        }
        body.innerHTML = '';
        body.appendChild(tpl.content.cloneNode(true));
        title.textContent = titleText || defaultTitle;
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    Array.prototype.forEach.call(document.querySelectorAll('.le-view-license'), function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            openModal(link.getAttribute('data-target'), link.getAttribute('data-title'));
        });
    });

    modal.querySelector('.le-modal-close').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
})();
</script>
