<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\Exception\MasterNotFoundException;
use App\Tests\Common\AbstractFunctionalTestCase;
use DOMDocument;
use Psr\Http\Message\ResponseInterface;

class MasterControllerTest extends AbstractFunctionalTestCase
{
    public function testCreateEditAndDeleteMaster()
    {
        $firstName = 'Иван';
        $lastName = 'Иванов';
        $phone = '+78887776655';

        $masterId = $this->doCreateMaster(
            firstName: $firstName,
            lastName: $lastName,
            phone: $phone
        );

        $masterBody = $this->doGetMaster($masterId);
        $tagsArray = $this->findMasterFields($masterBody);

        $this->assertMaster(['fullName' => $firstName . ' ' . $lastName, 'phone' => $phone], $tagsArray);

        $newFirstName = 'Максим';
        $newLastName = 'Максимов';
        $newPhone = '+79995558844';

        $masterId = $this->doEditMaster(
            id: $masterId,
            firstName: $newFirstName,
            lastName: $newLastName,
            phone: $newPhone
        );

        $newMasterBody = $this->doGetMaster($masterId);
        $newTagsArray = $this->findMasterFields($newMasterBody);

        $this->assertMaster(['fullName' => $newFirstName . ' ' . $newLastName, 'phone' => $newPhone], $newTagsArray);

        $this->doDeleteMaster($masterId);

        $this->assertMasterNotFount($masterId);
    }

    private function doGetMaster(int $masterId): string
    {
        $response = $this->sendGetRequest(
            '/master/edit',
            ['master_id' => $masterId]
        );

        $this->assertStatusCode(200, $response);
        $body = $this->parseResponseBodyAsHtml($response);
        return $body;
    }

    private function doCreateMaster(string $firstName, string $lastName, string $phone): int
    {
        $response = $this->sendPostRequest(
            '/master/create',
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone'=> $phone
            ]
        );

        $this->assertStatusCode(302, $response);

        $location = $response->getHeaderLine('Location');
        $masterId = null;

        if ($location) {
            $parts = explode('?master_id=', $location);
            if (count($parts) > 1) {
                $masterId = (int) trim($parts[1], '/');
            }
        }

        $this->assertEquals('integer', gettype($masterId ?? null));

        return $masterId;
    }

    private function doDeleteMaster(int $masterId): void
    {
        $response = $this->sendPostRequest("/master/delete?master_id=$masterId", []);

        $this->assertStatusCode(302, $response);
    }

    private function doEditMaster(int $id, string $firstName, string $lastName, string $phone): int
    {
        $response = $this->sendPostRequest(
            "/master/update?master_id=$id",
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone'=> $phone
            ]
        );

        $this->assertStatusCode(302, $response);

        $location = $response->getHeaderLine('Location');
        $masterId = null;

        if ($location) {
            $parts = explode('?master_id=', $location);
            if (count($parts) > 1) {
                $masterId = (int) trim($parts[1], '/');
            }
        }

        $this->assertEquals('integer', gettype($masterId ?? null));

        return $masterId;
    }

    private function findMasterFields(string $html): array
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        $fieldsIds = ['first-name', 'last-name', 'phone'];

        $result = [];
        foreach ($fieldsIds as $id) {
            $element = $doc->getElementById($id);
            if ($element) {
                $result[$id] = $element->getAttribute('value');
            } else {
                $result[$id] = null;
            }
        }

        return $result;
    }

    private function assertMaster(array $expectedResult, array $obtainedResult): void
    {
        $this->assertEquals($expectedResult['fullName'], $obtainedResult['first-name'] . ' ' . $obtainedResult['last-name']);
        $this->assertEquals($expectedResult['phone'], $obtainedResult['phone']);
    }

    private function assertStatusCode(int $statusCode, ResponseInterface $response): void
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), "status code must be $statusCode");
    }

    private function assertThrows(\Closure $closure, string $exceptionClass): void
    {
        $actualExceptionClass = null;
        try
        {
            $closure();
        }
        catch (\Throwable $e)
        {
            $actualExceptionClass = $e::class;
        }
        $this->assertEquals($exceptionClass, $actualExceptionClass, "$exceptionClass exception should be thrown");
    }

    private function assertMasterNotFount(int $masterId): void
    {
        $this->assertThrows(
            static fn() => $this->sendGetRequest(
                '/master/edit',
                ['master_id' => $masterId]
            ),
            MasterNotFoundException::class
        );
    }
}