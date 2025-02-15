<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstimateValidated extends Mailable
{
    use Queueable, SerializesModels;

    public $estimate;
    public $validator;

    /**
     * Create a new message instance.
     *
     * @param $estimate
     * @param $validator
     */
    public function __construct($estimate, $validator)
    {
        $this->estimate = $estimate;
        $this->validator = $validator;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $estimateViewUrl = url('/estimate/view/' . $this->estimate->estimate_number);

        return $this->view('emails.estimateValidated')
                    ->subject('votre demande a été validée')
                    ->with([
                        'estimate' => $this->estimate,
                        'validator' => $this->validator,
                        'estimateViewUrl' => $estimateViewUrl,
                    ]);
    }
}

