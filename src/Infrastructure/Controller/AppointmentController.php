<?php

namespace ReservationSystem\Infrastructure\Controller;

use ReservationSystem\Application\Handlers\AppointmentService;
use ReservationSystem\Application\Services\FakeEventDispatcher;
use ReservationSystem\Domain\Exception\DomainException;
use ReservationSystem\Infrastructure\Repository\SQLAppointmentRepository;

class AppointmentController
{
    /**
     * @param AppointmentService $appointmentService
     */
    public function __construct(private readonly AppointmentService $appointmentService)
    {
    }

    /**
     * @return AppointmentController
     */
    public static function createDefault():AppointmentController
    {
        return new self(
            new AppointmentService(new SQLAppointmentRepository(), new FakeEventDispatcher())
        );
    }

    /**
     * @param string $requestMethod
     * @param array $requestBody
     * @return void
     * @throws \JsonException
     */
    public function processRequest(string $requestMethod, array $requestBody): void
    {
        $response = match ($requestMethod) {
            'GET' => $this->appointments(),
            'POST' => $this->scheduleAppointment($requestBody['schedule'] ?? '', $requestBody['userId'] ?? ''),
            'DELETE' => $this->cancelAppointment($requestBody['schedule'] ?? '', $requestBody['userId'] ?? ''),
            default => $this->notFoundResponse(),
        };
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    /**
     * @return array
     */
    private function appointments():array
    {
        $result = $this->appointmentService->appointments();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(["data" => $result]);
        return $response;
    }

    /**
     * @param string $scheduleDate
     * @param string $userId
     * @return array
     * @throws \JsonException
     */
    private function scheduleAppointment(string $scheduleDate, string $userId): array
    {

        try {
            $this->appointmentService->scheduleAppointment($userId, new \DateTime('@'.strtotime($scheduleDate)));
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = null;
            return $response;
        }catch (DomainException $exception){
            return $this->unprocessableEntityResponse($exception->getMessage(), "400");
        }
    }



    /**
     * @param string $scheduleDate
     * @param string $userId
     * @return array
     * @throws \JsonException
     */
    private function cancelAppointment(string $scheduleDate, string $userId): array
    {
        try {
            $this->appointmentService->cancelAppointment($userId, new \DateTime('@'.strtotime($scheduleDate)));
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = null;
            return $response;
        }catch (DomainException $exception){
            return $exception->getCode() === 404 ? $this->notFoundResponse() : $this->unprocessableEntityResponse($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param string|null $message
     * @param string $code
     * @return array
     * @throws \JsonException
     */
    private function unprocessableEntityResponse(?string $message = 'Invalid input', string $code = "422"): array
    {
        $response['status_code_header'] = "HTTP/1.1 {$code} Unprocessable Entity";
        $response['body'] = json_encode([
            'error' => $message
        ], JSON_THROW_ON_ERROR);
        return $response;
    }

    /**
     * @return array
     */
    private function notFoundResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}