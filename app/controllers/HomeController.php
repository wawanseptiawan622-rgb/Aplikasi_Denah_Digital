<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home', [
            'title'      => $this->config['app_name'],
            'facilities' => Facility::all(),
            'categories' => Facility::categories(),
            'dataUpdatedAt' => Facility::lastUpdatedAt(),
        ]);
    }

    /** Endpoint JSON: /api/facilities */
    public function facilities(): void
    {
        $this->json([
            'categories' => Facility::categories(),
            'data'       => Facility::all(),
        ]);
    }
}
