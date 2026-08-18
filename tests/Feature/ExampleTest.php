<?php

it('redirects guests from the dashboard to the login screen', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
