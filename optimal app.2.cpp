#include <iostream>
#include <vector>
#include <algorithm>
using namespace std;

class Solution {
public:
    int maxArea(vector<int>& height) {
        int maxWater = 0;
        int lp = 0, rp = height.size() - 1;

        while (lp < rp) {
            int width = rp - lp;
            int ht = min(height[lp], height[rp]);

            maxWater = max(maxWater, width * ht);

            if (height[lp] < height[rp])
                lp++;
            else
                rp--;
        }

        return maxWater;
    }
};

