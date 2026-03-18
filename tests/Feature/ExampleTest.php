<?php

it('redirects home page', function () {
    $response = $this->get('/');

    $response->assertRedirect();
});
