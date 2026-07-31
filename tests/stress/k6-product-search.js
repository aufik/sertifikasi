import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '10s', target: 10 },
    { duration: '30s', target: 50 },
    { duration: '10s', target: 0 },
  ],
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<800'],
  },
};

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';
const TOKEN = __ENV.API_TOKEN || '';

export default function () {
  const params = {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${TOKEN}`,
    },
  };

  const response = http.get(`${BASE_URL}/api/v1/products?search=SKU&per_page=15`, params);

  check(response, {
    'status is 200': (r) => r.status === 200,
    'has response time header': (r) => Boolean(r.headers['X-Response-Time-Ms']),
  });

  sleep(1);
}
