<?php
if (isset($__request) && is_a($__request, 'Piece_Unity_Request')
    && isset($__session) && is_a($__session, 'Piece_Unity_Session_Common')
    ) {
    print 'OK';
} else {
    print 'NG';
}
?>