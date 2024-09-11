<?php

namespace App\Http\Controllers\User;

use App\Domain\File\Csv\CsvDataValidator;
use App\Domain\File\UserSpreadsheet\UserSpreadsheet;
use App\Domain\User\User;
use App\Domain\User\UserDataValidator;
use App\Exceptions\CsvEmptyContentException;
use App\Exceptions\CsvHeadersValidation;
use App\Exceptions\DataValidationException;
use App\Exceptions\DuplicatedDataException;
use App\Exceptions\InvalidUserObjectException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserSpreadsheetException;
use App\Http\Controllers\Controller;
use App\Http\Helpers\DateTime;
use App\Infra\Db\UserDb;
use App\Infra\File\Csv\Csv;
use App\Infra\Uuid\UuidGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/user/spreadsheet",
     *     summary="Importação de usuário através de arquivo CSV",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="text/csv",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     default="Campo do tipo arquivo, com o nome 'file', que recebe o arquivo CSV com os dados dos usuários"
     *                 ),
     *           ),
     *        )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Created",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="created_users",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="date_time",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "created_users": 2,
     *                        "date_time": "2023-12-28 04:10:10"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Bad Request",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="bad_request",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "bad_request": "Spreadsheet error: line 2 | CPF already created",
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function spreadsheet(Request $request): JsonResponse
    {
        try {
            $this->validate($request, ['file' => 'required']);

            $uploadedFile = $request->file('file');

            $csv = new Csv();

            $csv
                ->setDataValidator(new CsvDataValidator())
                ->setMimeType($uploadedFile->getClientMimeType())
                ->setSizeInBytes($uploadedFile->getSize())
                ->setContent($uploadedFile->getContent())
            ;

            $userSpreadsheet = new UserSpreadsheet();

            $userSpreadsheet
                ->setUuidGenerator(new UuidGenerator())
                ->setUserPersistence(new UserDb())
                ->setCsv($csv)
            ;

            $usersFromFile = $userSpreadsheet->buildUsersFromContent();

            (new User(new UserDb()))->createFromBatch($usersFromFile);

            return $this->buildCreatedResponse([
                'created_users' => count($usersFromFile),
                'date_time' => DateTime::formatDateTime('now')
            ]);
        } catch (DataValidationException | CsvHeadersValidation $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (UserSpreadsheetException | DuplicatedDataException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (InvalidUserObjectException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *     path="/user",
     *     summary="Listagem de todos os usuários cadastrados",
     *     tags={"User"},
     *     @OA\Response(
     *          response="201",
     *          description="Created",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="id",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="cpf",
     *                         type="string",
     *                     ),
     *                     example={
     *                        {
     *                          "id": "a38a7ac8-9295-33c2-8c0b-5767c1449bc3",
     *                          "name": "Ronaldo de Assis Moreira",
     *                          "email": "ro.naldinho@email.com",
     *                          "cpf": "2023-12-28 04:10:10"
     *                        }
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function all(Request $request): JsonResponse
    {
        try {
            $user = new User(new UserDb());

            $users = $user->findAll();

            $response = [];
            foreach($users as $user) {
                $response[] = [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'cpf' => $user->getCpf()
                ];
            }

            return $this->buildSuccessResponse($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Patch(
     *     path="/user/{id}/name",
     *     summary="Editar nome do usuário",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do usuário",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default=""
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 example={
     *                     "name": "Paolo Maldini"
     *                 }
     *             ),
     *        )
     *    ),
     *    @OA\Response(
     *          response="200",
     *          description="OK",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="date_time",
     *                         type="string",
     *                     ),
     *                     example={
     *                       "name": "Paolo Maldini",
     *                       "date_time": "2023-12-28 10:55:14"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad Request",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="bad_request",
     *                         type="string",
     *                     ),
     *                     example={
     *                         "bad_request": "The user does not exist"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function editName(Request $request, string $id): JsonResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required|max:100'
            ]);

            $user = new User(new UserDb());

            $user
                ->setDataValidator(new UserDataValidator())
                ->setId($id)
                ->setName($request->post('name'))
            ;

            $user->editName();

            return $this->buildSuccessResponse([
                'name' => $user->getName(),
                'date_time' => $user->getDateEdition()
            ]);
        } catch (UserNotFoundException | DataValidationException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Patch(
     *     path="/user/{id}/cpf",
     *     summary="Editar CPF do usuário",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do usuário",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default=""
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="cpf",
     *                     type="string"
     *                 ),
     *                 example={
     *                     "cpf": "32016170085"
     *                 }
     *             ),
     *        )
     *    ),
     *    @OA\Response(
     *          response="200",
     *          description="OK",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="cpf",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="date_time",
     *                         type="string",
     *                     ),
     *                     example={
     *                       "cpf": "32016170085",
     *                       "date_time": "2023-12-29 11:50:02"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad Request",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="bad_request",
     *                         type="string",
     *                     ),
     *                     example={
     *                         "bad_request": "The user does not exist"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function editCpf(Request $request, string $id): JsonResponse
    {
        try {
            $this->validate($request, [
                'cpf' => 'required|max:11'
            ]);

            $user = new User(new UserDb());

            $user
                ->setDataValidator(new UserDataValidator())
                ->setId($id)
                ->setCpf($request->post('cpf'))
            ;

            $user->editCpf();

            return $this->buildSuccessResponse([
                'cpf' => $user->getCpf(),
                'date_time' => $user->getDateEdition()
            ]);
        } catch (UserNotFoundException | DataValidationException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (DuplicatedDataException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Patch(
     *     path="/user/{id}/email",
     *     summary="Editar email do usuário",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do usuário",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             default=""
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 example={
     *                     "email": "random@email.com"
     *                 }
     *             ),
     *        )
     *    ),
     *    @OA\Response(
     *          response="200",
     *          description="OK",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="date_time",
     *                         type="string",
     *                     ),
     *                     example={
     *                       "email": "random@email.com",
     *                       "date_time": "2023-12-25 08:13:25"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad Request",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="bad_request",
     *                         type="string",
     *                     ),
     *                     example={
     *                         "bad_request": "The user does not exist"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function editEmail(Request $request, string $id): JsonResponse
    {
        try {
            $this->validate($request, [
                'email' => 'required|max:100|email'
            ]);

            $user = new User(new UserDb());

            $user
                ->setDataValidator(new UserDataValidator())
                ->setId($id)
                ->setEmail($request->post('email'))
            ;

            $user->editEmail();

            return $this->buildSuccessResponse([
                'email' => $user->getEmail(),
                'date_time' => $user->getDateEdition()
            ]);
        } catch (UserNotFoundException | DataValidationException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (DuplicatedDataException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *     path="/user/spreadsheet",
     *     summary="Geração dos dados dos usuários registrados em formato CSV",
     *     tags={"User"},
     *     @OA\Response(
     *          response="200",
     *          description="Created",
     *          content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="csv",
     *                         type="string",
     *                     ),
     *                     example={
     *                        "csv": "name,cpf,email\nRonaldo de Assis Moreira,16742019077,drnaoseioque@email.com\n"
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     * )
     */
    public function createSpreadsheet(Request $request): JsonResponse
    {
        try {
            $user = new User(new UserDb());

            $userDataValidator = 'find-the-proper-class-to-validate-the-user-data';
            $user->setDataValidator($userDataValidator);

            $users = 'find-all-users';

            $csv = new Csv();

            $csvDataValidator = 'find-the-proper-class-to-validate-the-csv-data';

            $csv->setDataValidator($csvDataValidator);

            $userSpreadsheet = new UserSpreadsheet();

            $userSpreadsheet
                ->setUsers($users)
                ->setCsv($csv)
            ;

            $content = 'find-the-way-to-build-the-content';

            return $this->buildSuccessResponse([
                'csv' => $content
            ]);
        } catch (DataValidationException | CsvEmptyContentException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (UserSpreadsheetException $e) {
            return $this->buildBadRequestResponse($e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
