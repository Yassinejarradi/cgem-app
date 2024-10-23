<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstimateStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $estimate;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $estimate
     */
    public function __construct($user, $estimate)
    {
        $this->user = $user;
        $this->estimate = $estimate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Build the URL for viewing the estimate details
        $estimateViewUrl = url('/estimate/view/' . $this->estimate->estimate_number);

        return $this->view('emails.estimateMiseAjour')
                    ->subject('Mise à jour de votre demande')
                    ->with([
                        'user' => $this->user,
                        'estimate' => $this->estimate,
                        'estimateViewUrl' => $estimateViewUrl,
                    ]);
    }
}
