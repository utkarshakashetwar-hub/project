
#include <iostream>
#include <vector>
#include <algorithm>
using namespace std;

bool isPossible(vector<int>& boards, int n, int m, int maxTime) {
    int painters = 1;
    int time = 0;

    for (int i = 0; i < n; i++) {
        if (boards[i] > maxTime) {
            return false;
        }

        if (time + boards[i] <= maxTime) {
            time += boards[i];
        } else {
            painters++;
            time = boards[i];
        }
    }

    return painters <= m;
}

int minTime(vector<int>& boards, int n, int m) {
    if (m > n) return -1;

    int st = *max_element(boards.begin(), boards.end());
    int end = 0;

    for (int i = 0; i < n; i++) {
        end += boards[i];
    }

    int ans = end;

    while (st <= end) {
        int mid = st + (end - st) / 2;

        if (isPossible(boards, n, m, mid)) {
            ans = mid;
            end = mid - 1;
        } else {
            st = mid + 1;
        }
    }

    return ans;
}

int main() {
    vector<int> boards = {10, 20, 30, 40};
    int n = boards.size();
    int m = 2;

    cout << "Minimum time: " << minTime(boards, n, m);

    return 0;
}
