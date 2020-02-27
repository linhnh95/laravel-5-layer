<?php

namespace App\Common\Traits;

trait JsonResponseTrait
{

    /**
     * Return Data Json
     *
     * @param array $data
     * @param array $meta
     * @param array $link
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseJson($data = [], $meta = [], $link = [])
    {
        $responseData = [];

        $responseData['success'] = true;

        if (isset($data)) {
            $responseData['data'] = $data;
        }

        if (is_array($meta) && !empty($meta)) {
            $responseData['meta'] = $meta;
        }

        if (is_array($link) && !empty($link)) {
            $responseData['link'] = array_merge([
                'first' => null,
                'last' => null,
                'next' => null,
                'prev' => null,
            ], $link);
        }

        return response()->json($responseData);
    }

    /**
     * return True
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseJsonSuccess()
    {
        $responseData = [
            'success' => true,
            'data' => [
            ],
        ];
        return response()->json($responseData);
    }

    /**
     * return False
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseJsonFalse()
    {
        $responseData = [
            'success' => false,
            'data' => [
            ],
        ];
        return response()->json($responseData);
    }

    /**
     * Return Error
     *
     * @param $data
     * @param $errors
     * @param int $code
     * @param array $customData
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponse($data, $errors, $code = 200, $customData = [])
    {
        $response = [
            'success' => false,
            'data' => $data,
            'errors' => $errors
        ];

        if (count($customData)) {
            foreach ($customData as $key => $item) {
                $response[$key] = $item;
            }
        }

        return response()->json($response, $code);
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function buildMeta($data)
    {
        $response = [
            'total_item' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'first_item' => $data->firstItem(),
            'last_item' => $data->lastItem()
        ];
        return $response;
    }
}
