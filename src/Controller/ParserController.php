<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ParserController
{
  /**
  * @Route("/parse/this")
  */
  public function print()
  {
    $number = random_int(0, 100);

    return new Response(
      '<html><body>Your number: ' .$number. '</body></html>'
    );
  }
}

 ?>
