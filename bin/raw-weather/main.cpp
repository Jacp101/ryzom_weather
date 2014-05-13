
#if _WIN32 || _WIN64
	#if _WIN64
		#define cpuPlatform 32
	#else
		#define cpuPlatform 64
	#endif
#else // __GNUC__
	#if __x86_64__
		#define cpuPlatform 64
	#else
		#define cpuPlatform 32
	#endif
#endif

#include <stdio.h>

#include "nel/misc/fast_floor.h"
#include "nel/misc/noise_value.h"
#include "nel/misc/vector.h"

int main( int argc, char **argv ) {

	uint32 cycle, nbCycles = 5;
	if (argc == 1)
		cycle = 112952;
	else
	{
		if (argc >= 2)
			sscanf(argv[1], "%d", &cycle);
		if (argc >= 3)
			sscanf(argv[2], "%d", &nbCycles);
	}
	// nbCycles*9/(60*24*365) years worth
	// 1 year = 58400 weather cycles
	printf("; %dbit platform\n", cpuPlatform);
	printf("; cycle=%d, nbCycles=%d\n", cycle, nbCycles);

	NLMISC::CNoiseValue nv;

	NLMISC::OptFastFloorBegin();
	for(uint32 i=cycle; i < (cycle + nbCycles); ++i){
		float noiseValue = nv.eval(NLMISC::CVector(i * 0.99524f, i * 0.85422f, i * -0.45722f));
		noiseValue = fmodf(noiseValue * 10.f, 1.f);
		printf("%d, %f\n", i, noiseValue);
	}
	NLMISC::OptFastFloorEnd();


	return 0;
}

